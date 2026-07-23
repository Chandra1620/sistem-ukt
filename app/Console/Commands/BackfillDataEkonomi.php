<?php

namespace App\Console\Commands;

use App\Models\DataEkonomi;
use App\Models\SubKriteria;
use App\Support\PemetaanDataEkonomi;
use Illuminate\Console\Command;

/**
 * Hitung ulang kolom deskriptif (penghasilan_*, jumlah_tanggungan, luas_tanah,
 * pbb, hutang_cicilan, tagihan_*, kepemilikan_*, pekerjaan_*) untuk baris
 * data_ekonomi yang SUDAH ADA, memakai pemetaan terbaru (PemetaanDataEkonomi).
 *
 * Dipakai sekali setelah memperbaiki trait, agar data lama ikut benar TANPA
 * perlu import ulang. Tidak menyentuh sub_c*_id maupun perhitungan SAW.
 */
class BackfillDataEkonomi extends Command
{
    use PemetaanDataEkonomi;

    protected $signature = 'ekonomi:backfill {--dry-run : Hitung tanpa menyimpan}';

    protected $description = 'Hitung ulang kolom deskriptif data_ekonomi dari sub kriteria terpilih';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        // Preload semua sub kriteria + kriteria (untuk ambil kode C1..C12)
        $subById = SubKriteria::with('kriteria')->get()->keyBy('id');
        $this->info('Sub kriteria dimuat: ' . $subById->count());

        $total = 0;
        $berubah = 0;

        DataEkonomi::query()->chunkById(200, function ($rows) use ($subById, $dry, &$total, &$berubah) {
            foreach ($rows as $de) {
                $total++;

                $subByKode = [];
                for ($i = 1; $i <= 12; $i++) {
                    $sid = $de->{'sub_c' . $i . '_id'};
                    if ($sid && isset($subById[$sid])) {
                        $sub  = $subById[$sid];
                        $kode = $sub->kriteria->kode ?? ('C' . $i);
                        $subByKode[strtoupper($kode)] = $sub;
                    }
                }

                if (empty($subByKode)) {
                    continue;
                }

                $fields = $this->petakanFieldEkonomi($subByKode);

                $de->fill($fields);
                if ($de->isDirty()) {
                    $berubah++;
                    if (!$dry) {
                        $de->save();
                    }
                }
            }
        });

        $this->info(($dry ? '[DRY-RUN] ' : '') . "Selesai. Diproses: {$total}, berubah: {$berubah}.");

        return self::SUCCESS;
    }
}
