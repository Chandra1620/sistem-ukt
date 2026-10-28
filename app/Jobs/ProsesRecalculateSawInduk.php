<?php

namespace App\Jobs;

use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Services\SawService;
use App\Services\AuditLogService;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProsesRecalculateSawInduk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 600;

    /**
     * @param int $periodeId ID Periode Seleksi yang akan dihitung ulang
     * @param int|null $userId ID user admin yang memicu recalculate (untuk audit log)
     */
    public function __construct(
        public int $periodeId,
        public ?int $userId = null
    ) {}

    public function handle(SawService $sawService): void
    {
        $periode = PeriodeSeleksi::find($this->periodeId);
        if (!$periode) {
            Log::warning("ProsesRecalculateSawInduk dibatalkan: Periode {$this->periodeId} tidak ditemukan.");
            return;
        }

        // Cek apakah ada pendaftaran yang eligible (hanya yang belum final)
        $eligibleQuery = Pendaftaran::where('periode_id', $this->periodeId)
            ->whereIn('status', [
                'diverifikasi',
                'sedang_dihitung',
                'hasil_awal',
            ]);

        $totalCount = (clone $eligibleQuery)->count();
        if ($totalCount === 0) {
            Log::info("ProsesRecalculateSawInduk: Tidak ada pendaftaran eligible pada periode {$this->periodeId}.");
            return;
        }

        $skippedFinalCount = Pendaftaran::where('periode_id', $this->periodeId)
            ->whereIn('status', ['final_ukt', 'selesai'])
            ->count();
        if ($skippedFinalCount > 0) {
            Log::info("ProsesRecalculateSawInduk: {$skippedFinalCount} pendaftaran final dilewati pada periode {$this->periodeId}.");
        }

        // 1. Hitung nilai min/max seluruh peserta SEKALI di awal
        $kriteriaData = $sawService->getStatistikKriteriaPeriode($this->periodeId);

        // Tandai status sedang_dihitung
        (clone $eligibleQuery)->update(['status' => 'sedang_dihitung']);

        // 2. Pecah mahasiswa per chunk (chunkById 100) dan buat list job chunk
        $batchJobs = [];
        (clone $eligibleQuery)
            ->select('id')
            ->chunkById(100, function ($pendaftarans) use (&$batchJobs, $kriteriaData) {
                $chunkIds = $pendaftarans->pluck('id')->all();
                if (!empty($chunkIds)) {
                    $batchJobs[] = new HitungSawChunkJob($chunkIds, $this->periodeId, $kriteriaData);
                }
            });

        if (empty($batchJobs)) {
            Log::info("ProsesRecalculateSawInduk: List chunk job kosong untuk periode {$this->periodeId}.");
            return;
        }

        $periodeId = $this->periodeId;
        $userId    = $this->userId;
        $total     = $totalCount;
        $batchName = "Recalculate SAW - Periode {$periode->nama} ({$total} Mahasiswa)";

        // 3. Dispatch batch ke queue dengan callback then untuk update ranking 1 query
        $batch = Bus::batch($batchJobs)
            ->name($batchName)
            ->allowFailures()
            ->then(function (Batch $batch) use ($periodeId, $userId, $total) {
                // Update ranking 1 query window function setelah seluruh chunk selesai
                app(SawService::class)->updateRankingPeriode($periodeId);

                // Catat riwayat audit
                AuditLogService::log(
                    'recalculate_saw',
                    'PeriodeSeleksi',
                    $periodeId,
                    null,
                    [
                        'jumlah'   => $total,
                        'batch_id' => $batch->id,
                        'status'   => 'selesai',
                    ],
                    $userId
                );

                Log::info("Recalculate SAW selesai untuk periode {$periodeId} (Total: {$total}, Batch: {$batch->id})");
            })
            ->catch(function (Batch $batch, Throwable $e) use ($periodeId) {
                Log::error("Recalculate SAW batch gagal pada periode {$periodeId}: " . $e->getMessage());
            })
            ->finally(function (Batch $batch) use ($periodeId) {
                Cache::forget("recalculate_saw_active_batch_{$periodeId}");
            })
            ->dispatch();

        // Simpan batch ID aktif di Cache untuk tracking status/progres (opsional)
        Cache::put("recalculate_saw_active_batch_{$periodeId}", $batch->id, now()->addHours(6));
        Cache::put("recalculate_saw_latest_batch", $batch->id, now()->addHours(6));

        Log::info("Recalculate SAW batch dimulai: Batch ID {$batch->id}, Total {$total} mahasiswa, " . count($batchJobs) . " chunks.");
    }
}
