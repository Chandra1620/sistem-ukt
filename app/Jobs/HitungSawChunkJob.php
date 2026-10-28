<?php

namespace App\Jobs;

use App\Services\SawService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HitungSawChunkJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 300;

    /**
     * @param array<int> $pendaftaranIds Daftar ID pendaftaran dalam chunk ini (maks. 100)
     * @param int $periodeId ID Periode Seleksi
     * @param array $kriteriaData Data statistik kriteria dan rentang golongan UKT (precalculated)
     */
    public function __construct(
        public array $pendaftaranIds,
        public int $periodeId,
        public array $kriteriaData
    ) {}

    public function handle(SawService $sawService): void
    {
        // Jika batch dibatalkan, hentikan eksekusi
        if ($this->batch()?->cancelled()) {
            return;
        }

        try {
            $sawService->prosesChunk($this->pendaftaranIds, $this->periodeId, $this->kriteriaData);
        } catch (\Throwable $e) {
            Log::error("Error saat memproses chunk SAW periode {$this->periodeId}: " . $e->getMessage(), [
                'pendaftaran_ids_count' => count($this->pendaftaranIds),
                'exception'             => $e,
            ]);
            throw $e;
        }
    }
}
