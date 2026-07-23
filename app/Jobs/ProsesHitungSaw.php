<?php

namespace App\Jobs;

use App\Models\Pendaftaran;
use App\Services\SawService;
use App\Services\NotifikasiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProsesHitungSaw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        public Pendaftaran $pendaftaran,
        public bool $updateRanking = true
    ) {}

    public function handle(SawService $sawService): void
    {
        try {
            $pendaftaran = $this->pendaftaran->fresh([
                'dataEkonomi',
                'programStudi',
                'periode',
            ]);

            if (!$pendaftaran) {
                Log::warning("Pendaftaran {$this->pendaftaran->id} tidak ditemukan, job dibatalkan.");
                return;
            }

            if (!$pendaftaran->dataEkonomi) {
                Log::warning("Data ekonomi tidak ditemukan untuk pendaftaran {$pendaftaran->id}, job dibatalkan.");
                return;
            }

            // Tandai sedang dihitung
            $pendaftaran->update(['status' => 'sedang_dihitung']);

            // Hitung SAW atau KIP bypass
            // SawService sudah menangani: simpan hasil_saw, detail_saw,
            // hasil_ukt, update ranking, dan update status ke 'hasil_awal'
            $hasil = $sawService->hitung($pendaftaran, $this->updateRanking);

            // Kirim notifikasi
            // Kirim notifikasi
            $tipe     = $hasil['type'] === 'kip' ? 'KIP' : 'Non-KIP';
            $hasilUkt = $pendaftaran->hasilUkt()->first();

            if ($hasilUkt) {
                NotifikasiService::hasilUktKeluar(
                    $pendaftaran,
                    $hasilUkt->golongan_final,
                    (float) $hasilUkt->nominal_final
                );
            }

            Log::info("Berhasil hitung UKT [{$tipe}] untuk pendaftaran {$pendaftaran->id}");
        } catch (\Exception $e) {
            Log::error("Error hitung SAW pendaftaran {$this->pendaftaran->id}: " . $e->getMessage());
            throw $e; // wajib di-rethrow agar queue mencatat failure dan retry berjalan
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("Job gagal permanen untuk pendaftaran {$this->pendaftaran->id}: " . $e->getMessage());

        // Kembalikan status agar tidak stuck di 'sedang_dihitung'
        $pendaftaran = $this->pendaftaran->fresh();
        $pendaftaran?->update(['status' => 'diverifikasi']); // sudah lolos verifikasi, cukup ulang hitung

        // Beri tahu admin agar bisa hitung ulang (jangan gagal diam-diam)
        if ($pendaftaran) {
            \App\Services\NotifikasiService::sawGagal($pendaftaran); // tambahkan method ini
            Log::channel('stack')->critical("SAW gagal untuk pendaftaran {$pendaftaran->id}, perlu recalculate manual.");
        }
    }
}
