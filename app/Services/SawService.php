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
            ", [
                $pendaftaran->id,
                0, // golongan = 0 untuk KIP
                0,
                $nominal,
                "KIP - Akreditasi {$prodi->akreditasi}",
                now(),
                now(),
            ]);

            $pendaftaran->update(['status' => 'hasil_awal']);
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
            $pendaftaran->update(['status' => 'hasil_awal']);
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
        $golongan = GolonganUkt::orderBy('golongan')
            ->get()
            ->first(function ($g) use ($skor) {
                $min = $g->skor_min !== null ? (float) $g->skor_min : null;
                $max = $g->skor_max !== null ? (float) $g->skor_max : null;

                if ($min !== null && $max !== null) {
                    return $skor >= $min && $skor <= $max;
                }

                return false;
            });

        if ($golongan) {
            return $golongan->golongan;
        }

        // Tidak ada golongan yang cocok -> konfigurasi golongan_ukt kemungkinan berlubang/kosong.
        \Illuminate\Support\Facades\Log::warning(
            "Skor SAW {$skor} tidak masuk range golongan mana pun. Periksa tabel golongan_ukt."
        );

        // Fallback aman: golongan terendah, tapi masalah tetap tercatat untuk ditindaklanjuti admin
        return \App\Models\GolonganUkt::min('golongan') ?? 1;
    }

    private function updateRanking(int $periodeId): void
    {
        // Ranking hanya untuk Non-KIP (yang punya hasil_saw)
        // Ranking 1 = skor tertinggi = paling mampu secara ekonomi
        $hasil = HasilSaw::where('periode_id', $periodeId)
            ->orderBy('skor_akhir', 'desc')
            ->get();

        foreach ($hasil as $i => $h) {
            $h->update(['ranking' => $i + 1]);
        }
    }
}
