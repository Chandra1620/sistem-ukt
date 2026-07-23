<?php

namespace App\Support;

use App\Models\SubKriteria;

/**
 * Menurunkan field ekonomi (penghasilan, tanggungan, pekerjaan, dll) dari
 * sub kriteria terpilih (C1..C12). Logikanya sama dengan jalur input manual
 * (Mahasiswa\DataEkonomiController) supaya hasil ETL & manual konsisten.
 */
trait PemetaanDataEkonomi
{
    /**
     * @param array<string,SubKriteria|null> $subByKode contoh: ['C1' => SubKriteria, ...]
     */
    protected function petakanFieldEkonomi(array $subByKode): array
    {
        $out = [];

        foreach ($subByKode as $kode => $sub) {
            if (!$sub) {
                continue;
            }
            $kode = strtoupper(trim($kode));

            $angka = $this->angkaWakilDariLabel((string) $sub->label);

            match ($kode) {
                'C1'  => $out['penghasilan_ayah']      = $angka,
                'C2'  => $out['penghasilan_ibu']       = $angka,
                'C3'  => $out['jumlah_tanggungan']     = (int) $angka,
                'C4'  => $out['tagihan_listrik']       = $angka,
                'C5'  => $out['pekerjaan_ayah']        = $this->mapPekerjaanAyah((string) $sub->label),
                'C6'  => $out['pekerjaan_ibu']         = $this->mapPekerjaanIbu((string) $sub->label),
                'C7'  => $out['kepemilikan_rumah']     = $this->mapRumah((string) $sub->label),
                'C8'  => $out['kepemilikan_kendaraan'] = $this->mapKendaraan((string) $sub->label),
                'C9'  => $out['luas_tanah']            = $angka,
                'C10' => $out['pbb']                   = $angka,
                'C11' => $out['hutang_cicilan']        = $angka,
                'C12' => $out['tagihan_air']           = $angka,
                default => null,
            };
        }

        $out['penghasilan_total'] = (float) ($out['penghasilan_ayah'] ?? 0)
            + (float) ($out['penghasilan_ibu'] ?? 0);

        return $out;
    }

    private function angkaWakilDariLabel(string $label): float
    {
        $label = trim($label);

        if (stripos($label, 'tidak') !== false) {
            return 0.0;
        }

        if (stripos($label, 'Rp') !== false) {
            preg_match_all('/[Rr][Pp]\s*[\d.,]+/u', $label, $m);
            $angka = array_map(fn($t) => (float) preg_replace('/\D/', '', $t), $m[0]);
        } else {
            preg_match_all('/\d[\d.]*/u', $label, $m);
            $angka = array_map(fn($t) => (float) str_replace('.', '', $t), $m[0]);
        }

        $angka = array_values(array_filter($angka, fn($n) => $n > 0));
        if (count($angka) === 0) {
            return 0.0;
        }

        $adaKurang = str_contains($label, '<') || str_contains($label, '≤');
        $adaLebih  = str_contains($label, '>') || str_contains($label, '≥');

        if (count($angka) >= 2) {
            return (min($angka) + max($angka)) / 2;
        }

        $n = $angka[0];
        if ($adaKurang) {
            return $n / 2;
        }
        if ($adaLebih) {
            return $n;
        }
        return $n;
    }

    private function mapRumah(string $label): string
    {
        $l = mb_strtolower(trim($label));

        return match (true) {
            str_contains($l, 'milik')                            => 'milik_sendiri',
            str_contains($l, 'dinas')                            => 'dinas',
            str_contains($l, 'sewa') || str_contains($l, 'kontrak') => 'sewa',
            default                                              => 'menumpang',
        };
    }

    private function mapKendaraan(string $label): string
    {
        $l = mb_strtolower(trim($label));

        // Urutan penting: cek "tidak" & ">"/"lebih" DULU sebelum motor/mobil,
        // agar "> 2 kendaraan (mobil/motor)" masuk ke 'lebih', bukan 'motor_dan_mobil'.
        return match (true) {
            str_contains($l, 'tidak')                            => 'tidak_ada',
            str_contains($l, '>') || str_contains($l, 'lebih')   => 'lebih',
            str_contains($l, 'motor') && str_contains($l, 'mobil') => 'motor_dan_mobil',
            str_contains($l, 'mobil')                            => 'mobil',
            str_contains($l, 'motor')                            => 'motor',
            default                                              => 'tidak_ada',
        };
    }

    private function mapPekerjaanAyah(string $label): string
    {
        $l = mb_strtolower(trim($label));

        // "wiraswasta" harus dicek sebelum "swasta" (karena mengandung kata "swasta").
        return match (true) {
            str_contains($l, 'pns') || str_contains($l, 'tni')
                || str_contains($l, 'polri') || str_contains($l, 'bumn') => 'pns',
            str_contains($l, 'wiraswasta') || str_contains($l, 'pengusaha')
                || str_contains($l, 'pedagang')                  => 'wiraswasta',
            str_contains($l, 'swasta')                           => 'swasta',
            str_contains($l, 'buruh')                            => 'buruh',
            str_contains($l, 'petani') || str_contains($l, 'nelayan') => 'petani',
            str_contains($l, 'meninggal')                        => 'meninggal',
            default                                              => 'tidak_bekerja',
        };
    }

    private function mapPekerjaanIbu(string $label): string
    {
        $l = mb_strtolower(trim($label));

        return match (true) {
            str_contains($l, 'ibu rumah') || str_contains($l, 'rumah tangga')
                || str_contains($l, 'irt')                       => 'ibu_rumah_tangga',
            str_contains($l, 'pns') || str_contains($l, 'tni')
                || str_contains($l, 'polri') || str_contains($l, 'bumn') => 'pns',
            str_contains($l, 'wiraswasta') || str_contains($l, 'pengusaha')
                || str_contains($l, 'pedagang')                  => 'wiraswasta',
            str_contains($l, 'swasta')                           => 'swasta',
            str_contains($l, 'buruh')                            => 'buruh',
            str_contains($l, 'petani') || str_contains($l, 'nelayan') => 'petani',
            str_contains($l, 'meninggal')                        => 'meninggal',
            default                                              => 'tidak_bekerja',
        };
    }
}
