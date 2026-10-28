<?php

namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\Kriteria;
use App\Models\HasilSaw;
use App\Models\DetailSaw;
use App\Models\SubKriteria;
use App\Models\GolonganUkt;
use Illuminate\Support\Facades\DB;

class SawService
{
    public function hitung(Pendaftaran $pendaftaran, bool $updateRanking = true): array
    {
        if ($pendaftaran->is_kip) {
            return $this->hitungKip($pendaftaran);
        }

        return $this->hitungNonKip($pendaftaran, $updateRanking);
    }

    /**
     * Update ranking satu periode. Dipanggil sekali setelah proses massal
     * (mis. import ETL) agar ranking tidak dihitung ulang tiap baris.
     */
    public function updateRankingPeriode(int $periodeId): void
    {
        $this->updateRanking($periodeId);
    }

    /**
    /**
     * Hitung statistik kriteria (min, max, bobot, tipe) dan data golongan UKT
     * untuk seluruh peserta dalam satu periode. HANYA DIJALANKAN SEKALI di awal.
     * Mengembalikan plain PHP array yang aman diserialisasi untuk queue batch.
     */
    public function getStatistikKriteriaPeriode(int $periodeId): array
    {
        $kriteria = Kriteria::where('periode_id', $periodeId)
            ->with('subKriteria')
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get();

        $nilaiSubById = [];
        $statistikKriteria = [];
        foreach ($kriteria as $k) {
            $nilaiList = [];
            foreach ($k->subKriteria as $s) {
                $nilaiSubById[$s->id] = (float) $s->nilai;
                $nilaiList[] = (float) $s->nilai;
            }
            $statistikKriteria[$k->id] = [
                'id'    => $k->id,
                'kode'  => $k->kode,
                'tipe'  => $k->tipe,
                'bobot' => (float) $k->bobot,
                'max'   => !empty($nilaiList) ? max($nilaiList) : 0.0,
                'min'   => !empty($nilaiList) ? min($nilaiList) : 0.0,
            ];
        }

        $golonganList = GolonganUkt::orderBy('skor_min')->get();
        $golonganRanges = [];
        $nominalByGolongan = [];
        foreach ($golonganList as $g) {
            $golonganRanges[] = [
                'golongan' => (int) $g->golongan,
                'skor_min' => $g->skor_min !== null ? (float) $g->skor_min : null,
                'skor_max' => $g->skor_max !== null ? (float) $g->skor_max : null,
            ];
            $nominalByGolongan[(int) $g->golongan] = (float) $g->nominal;
        }

        return [
            'statistikKriteria' => $statistikKriteria,
            'nilaiSubById'      => $nilaiSubById,
            'golonganRanges'    => $golonganRanges,
            'nominalByGolongan' => $nominalByGolongan,
        ];
    }

    /**
     * Cari golongan UKT berdasarkan range skor dari array yang sudah di-preload.
     * Rentang kontinu: skor >= skor_min dan skor < skor_max,
     * kecuali golongan teratas yang inklusif sampai skor_max (100).
     */
    public function tentukanGolonganDariArray(array $golonganRanges, float $skor): int
    {
        // Urutkan golongan berdasarkan skor_min
        usort($golonganRanges, function ($a, $b) {
            return ($a['skor_min'] ?? 0) <=> ($b['skor_min'] ?? 0);
        });

        $total = count($golonganRanges);

        foreach ($golonganRanges as $index => $g) {
            $min = $g['skor_min'] ?? null;
            $max = $g['skor_max'] ?? null;

            if ($min !== null && $max !== null) {
                $isTeratas = ($index === $total - 1);
                $matched = $isTeratas
                    ? ($skor >= $min && $skor <= $max)
                    : ($skor >= $min && $skor < $max);

                if ($matched) {
                    return (int) $g['golongan'];
                }
            }
        }

        \Illuminate\Support\Facades\Log::warning(
            "Skor SAW {$skor} tidak masuk rentang golongan mana pun. Periksa tabel golongan_ukt."
        );

        throw new \RuntimeException(
            "Skor SAW {$skor} tidak masuk rentang golongan mana pun. Periksa konfigurasi rentang skor pada tabel golongan_ukt."
        );
    }

    /**
     * Memproses satu chunk pendaftaran (mis. 100 mahasiswa).
     * Menggunakan statistik kriteria & golongan yang sudah dihitung SEKALI di awal,
     * sehingga tidak ada query berulang untuk kriteria/subkriteria/min/max.
     *
     * @param array<int> $pendaftaranIds
     */
    public function prosesChunk(array $pendaftaranIds, int $periodeId, array $kriteriaData): void
    {
        if (empty($pendaftaranIds)) {
            return;
        }

        $statistikKriteria = $kriteriaData['statistikKriteria'] ?? [];
        $nilaiSubById      = $kriteriaData['nilaiSubById'] ?? [];
        $golonganRanges    = $kriteriaData['golonganRanges'] ?? [];
        $nominalByGolongan = $kriteriaData['nominalByGolongan'] ?? [];

        $pendaftarans = Pendaftaran::whereIn('id', $pendaftaranIds)
            ->with(['dataEkonomi', 'programStudi', 'hasilUkt'])
            ->get();

        $now = now();
        $hasilSawRows = [];
        $hasilUktRows = [];
        $detailByPendaftaran = [];

        // Identifikasi pendaftaran yang dilindungi:
        // Pendaftaran yang sudah final ('final_ukt', 'selesai') ATAU memiliki hasil_ukt dengan is_override = true.
        $protectedPendaftaranIds = [];
        foreach ($pendaftarans as $p) {
            if (in_array($p->status, ['final_ukt', 'selesai']) || ($p->hasilUkt && $p->hasilUkt->is_override)) {
                $protectedPendaftaranIds[] = $p->id;
            }
        }

        foreach ($pendaftarans as $p) {
            if ($p->is_kip) {
                // Jangan sentuh hasil_ukt jika pendaftaran sudah final atau di-override
                if (!in_array($p->id, $protectedPendaftaranIds)) {
                    $prodi   = $p->programStudi;
                    $nominal = $prodi->nominal_kip ?? 0;
                    $hasilUktRows[] = [
                        'pendaftaran_id'       => $p->id,
                        'golongan_rekomendasi' => 0,
                        'golongan_final'       => 0,
                        'nominal_final'        => $nominal,
                        'sumber_pembiayaan'    => 'KIP',
                        'dasar_penetapan'      => 'KIP - Akreditasi ' . ($prodi->akreditasi ?? '-'),
                    ];
                }
                continue;
            }

            $dataEkonomi = $p->dataEkonomi;
            $skorAkhir   = 0.0;
            $details     = [];

            foreach ($statistikKriteria as $kId => $stat) {
                $subField = 'sub_' . strtolower($stat['kode']) . '_id';
                $subId    = $dataEkonomi->$subField ?? null;
                $nilai    = ($subId && isset($nilaiSubById[$subId])) ? $nilaiSubById[$subId] : 0.0;

                if ($stat['tipe'] === 'cost') {
                    $norm = $nilai > 0 ? ($stat['min'] / $nilai) : 0.0;
                } else {
                    $norm = $stat['max'] > 0 ? ($nilai / $stat['max']) : 0.0;
                }

                $terbobot   = $norm * ($stat['bobot'] / 100) * 100;
                $skorAkhir += $terbobot;

                $details[] = [
                    'kriteria_id'       => $kId,
                    'nilai_asli'        => $nilai,
                    'nilai_normalisasi' => $norm,
                    'nilai_terbobot'    => $terbobot,
                ];
            }

            $golongan = $this->tentukanGolonganDariArray($golonganRanges, $skorAkhir);
            $nominal  = $nominalByGolongan[$golongan] ?? 0;

            $detailByPendaftaran[$p->id] = $details;

            $hasilSawRows[] = [
                'pendaftaran_id'       => $p->id,
                'periode_id'           => $periodeId,
                'skor_akhir'           => $skorAkhir,
                'rekomendasi_golongan' => $golongan,
                'dihitung_pada'        => $now,
                'created_at'           => $now,
                'updated_at'           => $now,
            ];

            // Jangan sentuh hasil_ukt jika pendaftaran sudah final atau di-override
            if (!in_array($p->id, $protectedPendaftaranIds)) {
                $hasilUktRows[] = [
                    'pendaftaran_id'       => $p->id,
                    'golongan_rekomendasi' => $golongan,
                    'golongan_final'       => $golongan,
                    'nominal_final'        => $nominal,
                    'sumber_pembiayaan'    => 'Mandiri',
                    'dasar_penetapan'      => 'Non-KIP - SAW Score: ' . number_format($skorAkhir, 2),
                ];
            }
        }

        DB::transaction(function () use (
            $pendaftaranIds,
            $protectedPendaftaranIds,
            $hasilSawRows,
            $hasilUktRows,
            $detailByPendaftaran,
            $now
        ) {
            DetailSaw::whereHas('hasilSaw', fn($q) => $q->whereIn('pendaftaran_id', $pendaftaranIds))->delete();
            HasilSaw::whereIn('pendaftaran_id', $pendaftaranIds)->delete();

            // Hanya hapus hasil_ukt untuk pendaftaran yang BELUM final dan BUKAN override
            $targetUktPendaftaranIds = array_diff($pendaftaranIds, $protectedPendaftaranIds);

            if (!empty($targetUktPendaftaranIds)) {
                DB::table('hasil_ukt')
                    ->whereIn('pendaftaran_id', $targetUktPendaftaranIds)
                    ->whereRaw('"is_override" IS NOT TRUE')
                    ->delete();
            }

            if (!empty($hasilSawRows)) {
                foreach (array_chunk($hasilSawRows, 500) as $chunk) {
                    HasilSaw::insert($chunk);
                }

                $hasilSawIdByPendaftaran = HasilSaw::whereIn('pendaftaran_id', $pendaftaranIds)
                    ->pluck('id', 'pendaftaran_id');

                $detailRows = [];
                foreach ($detailByPendaftaran as $pendaftaranId => $details) {
                    $hasilSawId = $hasilSawIdByPendaftaran[$pendaftaranId] ?? null;
                    if (!$hasilSawId) {
                        continue;
                    }
                    foreach ($details as $d) {
                        $detailRows[] = array_merge($d, [
                            'hasil_saw_id' => $hasilSawId,
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ]);
                    }
                }

                if (!empty($detailRows)) {
                    foreach (array_chunk($detailRows, 500) as $chunk) {
                        DetailSaw::insert($chunk);
                    }
                }
            }

            if (!empty($hasilUktRows)) {
                foreach (array_chunk($hasilUktRows, 200) as $chunk) {
                    $placeholders = [];
                    $bindings     = [];
                    foreach ($chunk as $r) {
                        $placeholders[] = '(?, ?, ?, ?, ?, ?, FALSE, ?, ?)';
                        array_push(
                            $bindings,
                            $r['pendaftaran_id'],
                            $r['golongan_rekomendasi'],
                            $r['golongan_final'],
                            $r['nominal_final'],
                            $r['sumber_pembiayaan'],
                            $r['dasar_penetapan'],
                            $now,
                            $now
                        );
                    }
                    DB::statement(
                        'INSERT INTO hasil_ukt
                        (pendaftaran_id, golongan_rekomendasi, golongan_final,
                         nominal_final, sumber_pembiayaan, dasar_penetapan,
                         is_override, created_at, updated_at)
                      VALUES ' . implode(', ', $placeholders),
                        $bindings
                    );
                }
            }

            // HANYA update status ke 'hasil_awal' untuk pendaftaran yang belum final
            Pendaftaran::whereIn('id', $pendaftaranIds)
                ->whereNotIn('status', ['final_ukt', 'selesai'])
                ->update(['status' => 'hasil_awal']);
        });
    }

    /**
     * PERHITUNGAN SAW MASSAL (BULK) - dipakai oleh Import ETL.
     * Menggunakan preloading data kriteria sekali, memproses chunk,
     * dan mengupdate ranking 1x query window function.
     *
     * @param array<int> $pendaftaranIds
     */
    public function hitungMassal(array $pendaftaranIds, int $periodeId): void
    {
        if (empty($pendaftaranIds)) {
            return;
        }

        $kriteriaData = $this->getStatistikKriteriaPeriode($periodeId);
        $this->prosesChunk($pendaftaranIds, $periodeId, $kriteriaData);
        $this->updateRankingPeriode($periodeId);
    }

    /**
     * Versi yang menerima koleksi golongan yang sudah di-preload (untuk bulk).
     * Rentang kontinu: skor >= skor_min dan skor < skor_max,
     * kecuali golongan teratas yang inklusif sampai skor_max (100).
     */
    private function tentukanGolonganDari($golonganList, float $skor): int
    {
        $sorted = $golonganList->sortBy('skor_min')->values();
        $total  = $sorted->count();

        foreach ($sorted as $index => $g) {
            $min = $g->skor_min !== null ? (float) $g->skor_min : null;
            $max = $g->skor_max !== null ? (float) $g->skor_max : null;

            if ($min !== null && $max !== null) {
                $isTeratas = ($index === $total - 1);
                $matched = $isTeratas
                    ? ($skor >= $min && $skor <= $max)
                    : ($skor >= $min && $skor < $max);

                if ($matched) {
                    return (int) $g->golongan;
                }
            }
        }

        \Illuminate\Support\Facades\Log::warning(
            "Skor SAW {$skor} tidak masuk rentang golongan mana pun. Periksa tabel golongan_ukt."
        );

        throw new \RuntimeException(
            "Skor SAW {$skor} tidak masuk rentang golongan mana pun. Periksa konfigurasi rentang skor pada tabel golongan_ukt."
        );
    }

    /**
     * Hitung UKT untuk mahasiswa KIP.
     * Bypass SAW — nominal langsung dari akreditasi prodi.
     */
    private function hitungKip(Pendaftaran $pendaftaran): array
    {
        $pendaftaran->load('programStudi');
        $prodi   = $pendaftaran->programStudi;
        $nominal = $prodi->nominal_kip;

        DB::transaction(function () use ($pendaftaran, $nominal, $prodi) {
            // Hapus detail dulu sebelum header (hindari constraint violation)
            DetailSaw::whereHas(
                'hasilSaw',
                fn($q) =>
                $q->where('pendaftaran_id', $pendaftaran->id)
            )->delete();
            HasilSaw::where('pendaftaran_id', $pendaftaran->id)->delete();

            DB::statement("
                INSERT INTO hasil_ukt
                    (pendaftaran_id, golongan_rekomendasi, golongan_final,
                     nominal_final, sumber_pembiayaan, dasar_penetapan,
                     is_override, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'KIP', ?, FALSE, ?, ?)
                ON CONFLICT (pendaftaran_id)
                DO UPDATE SET
                    golongan_rekomendasi = EXCLUDED.golongan_rekomendasi,
                    golongan_final       = EXCLUDED.golongan_final,
                    nominal_final        = EXCLUDED.nominal_final,
                    sumber_pembiayaan    = EXCLUDED.sumber_pembiayaan,
                    dasar_penetapan      = EXCLUDED.dasar_penetapan,
                    updated_at           = EXCLUDED.updated_at
                WHERE hasil_ukt.is_override IS NOT TRUE
                  AND hasil_ukt.finalisasi_at IS NULL
            ", [
                $pendaftaran->id,
                0, // golongan = 0 untuk KIP
                0,
                $nominal,
                "KIP - Akreditasi {$prodi->akreditasi}",
                now(),
                now(),
            ]);

            if (!in_array($pendaftaran->status, ['final_ukt', 'selesai'])) {
                $pendaftaran->update(['status' => 'hasil_awal']);
            }
        });

        return [
            'type'    => 'kip',
            'nominal' => $nominal,
            'prodi'   => $prodi->nama,
        ];
    }

    /**
     * Hitung UKT untuk mahasiswa Non-KIP menggunakan metode SAW.
     */
    private function hitungNonKip(Pendaftaran $pendaftaran, bool $updateRanking = true): array
    {
        $periodeId   = $pendaftaran->periode_id;
        $kriteria    = Kriteria::where('periode_id', $periodeId)
            ->with('subKriteria')
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get();
        $dataEkonomi = $pendaftaran->dataEkonomi;

        $nilaiAsli        = $this->getNilaiAsli($dataEkonomi, $kriteria);
        $semuaNilai       = $this->getSemuaNilai($kriteria);
        $nilaiNormalisasi = $this->normalisasi($nilaiAsli, $semuaNilai, $kriteria);

        $skorAkhir = 0;
        $details   = [];

        foreach ($kriteria as $k) {
            $normalisasi = $nilaiNormalisasi[$k->id] ?? 0;
            $terbobot    = $normalisasi * ($k->bobot / 100) * 100;
            $skorAkhir  += $terbobot;

            $details[] = [
                'kriteria_id'       => $k->id,
                'nilai_asli'        => $nilaiAsli[$k->id] ?? 0,
                'nilai_normalisasi' => $normalisasi,
                'nilai_terbobot'    => $terbobot,
            ];
        }

        $golongan = $this->tentukanGolongan($skorAkhir);
        $nominal  = GolonganUkt::getNominalByGolongan($golongan);

        DB::transaction(function () use (
            $pendaftaran,
            $periodeId,
            $skorAkhir,
            $golongan,
            $nominal,
            $details,
            $updateRanking
        ) {
            $hasil = HasilSaw::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                [
                    'periode_id'           => $periodeId,
                    'skor_akhir'           => $skorAkhir,
                    'rekomendasi_golongan' => $golongan,
                    'dihitung_pada'        => now(),
                ]
            );

            DetailSaw::where('hasil_saw_id', $hasil->id)->delete();
            foreach ($details as $d) {
                DetailSaw::create(array_merge($d, ['hasil_saw_id' => $hasil->id]));
            }

            DB::statement("
                INSERT INTO hasil_ukt
                    (pendaftaran_id, golongan_rekomendasi, golongan_final,
                     nominal_final, sumber_pembiayaan, dasar_penetapan,
                     is_override, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'Mandiri', ?, FALSE, ?, ?)
                ON CONFLICT (pendaftaran_id)
                DO UPDATE SET
                    golongan_rekomendasi = EXCLUDED.golongan_rekomendasi,
                    golongan_final       = EXCLUDED.golongan_final,
                    nominal_final        = EXCLUDED.nominal_final,
                    sumber_pembiayaan    = EXCLUDED.sumber_pembiayaan,
                    dasar_penetapan      = EXCLUDED.dasar_penetapan,
                    updated_at           = EXCLUDED.updated_at
                WHERE hasil_ukt.is_override IS NOT TRUE
                  AND hasil_ukt.finalisasi_at IS NULL
            ", [
                $pendaftaran->id,
                $golongan,
                $golongan,
                $nominal,
                "Non-KIP - SAW Score: " . number_format($skorAkhir, 2),
                now(),
                now(),
            ]);

            if ($updateRanking) {
                $this->updateRanking($periodeId);
            }

            if (!in_array($pendaftaran->status, ['final_ukt', 'selesai'])) {
                $pendaftaran->update(['status' => 'hasil_awal']);
            }
        });

        return [
            'type'     => 'non_kip',
            'skor'     => $skorAkhir,
            'golongan' => $golongan,
            'nominal'  => $nominal,
        ];
    }

    private function getNilaiAsli($dataEkonomi, $kriteria): array
    {
        $nilaiAsli = [];

        foreach ($kriteria as $k) {
            $subField = 'sub_' . strtolower($k->kode) . '_id';
            $subId    = $dataEkonomi->$subField ?? null;

            if ($subId) {
                $sub               = SubKriteria::find($subId);
                $nilaiAsli[$k->id] = $sub ? (float) $sub->nilai : 0;
            } else {
                $nilaiAsli[$k->id] = 0;
            }
        }

        return $nilaiAsli;
    }

    private function getSemuaNilai($kriteria): array
    {
        $semuaNilai = [];

        foreach ($kriteria as $k) {
            $semuaNilai[$k->id] = SubKriteria::where('kriteria_id', $k->id)
                ->pluck('nilai')
                ->map(fn($v) => (float) $v)
                ->toArray();
        }

        return $semuaNilai;
    }

    private function normalisasi(array $nilaiAsli, array $semuaNilai, $kriteria): array
    {
        $hasil = [];

        foreach ($kriteria as $k) {
            $nilai = $nilaiAsli[$k->id] ?? 0;
            $semua = $semuaNilai[$k->id];

            // Hanya lewati bila daftar nilai memang kosong (bukan karena nilai = 0)
            if (empty($semua)) {
                $hasil[$k->id] = 0;
                continue;
            }

            $maxVal = max($semua);
            $minVal = min($semua); // jangan dipaksa jadi 1

            if ($k->tipe === 'cost') {
                // Cost: min / nilai. Lindungi hanya dari pembagian nol.
                $hasil[$k->id] = $nilai > 0 ? ($minVal / $nilai) : 0;
            } else {
                // Benefit: nilai / max
                $hasil[$k->id] = $maxVal > 0 ? ($nilai / $maxVal) : 0;
            }
        }

        return $hasil;
    }

    private function tentukanGolongan(float $skor): int
    {
        $golongan = GolonganUkt::orderBy('skor_min')->get();
        return $this->tentukanGolonganDari($golongan, $skor);
    }

    private function updateRanking(int $periodeId): void
    {
        // Ranking hanya untuk Non-KIP (yang punya hasil_saw pada periode tersebut).
        // Ranking 1 = skor tertinggi = paling mampu secara ekonomi.
        // Dioptimasi menjadi 1 query atomic UPDATE menggunakan PostgreSQL Window Function
        // di dalam DB::transaction, menggantikan loop N query UPDATE sebelumnya.
        DB::transaction(function () use ($periodeId) {
            DB::statement(
                'UPDATE hasil_saw AS h
                    SET ranking = r.rn,
                        updated_at = NOW()
                   FROM (
                        SELECT id,
                               ROW_NUMBER() OVER (ORDER BY skor_akhir DESC, id ASC) AS rn
                          FROM hasil_saw
                         WHERE periode_id = ?
                   ) AS r
                  WHERE h.id = r.id',
                [$periodeId]
            );
        });
    }
}
