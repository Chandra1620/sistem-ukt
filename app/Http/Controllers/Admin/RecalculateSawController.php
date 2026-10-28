<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Jobs\ProsesRecalculateSawInduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class RecalculateSawController extends Controller
{
    public function recalculate(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_seleksi,id',
        ]);

        $periode = PeriodeSeleksi::findOrFail($request->periode_id);

        // Cek apakah ada data pendaftaran yang memenuhi syarat perhitungan ulang
        $adaPendaftar = Pendaftaran::where('periode_id', $periode->id)
            ->whereIn('status', [
                'diverifikasi',
                'sedang_dihitung',
                'hasil_awal',
                'final_ukt',
                'selesai',
            ])
            ->exists();

        if (!$adaPendaftar) {
            return back()->with('error', 'Tidak ada data pendaftaran yang bisa dihitung ulang pada periode ini.');
        }

        // Dispatch job induk ke queue secara asinkron (tidak memblokir request HTTP)
        ProsesRecalculateSawInduk::dispatch($periode->id, Auth::id());

        return back()->with(
            'success',
            "Perhitungan sedang diproses di latar belakang untuk periode {$periode->nama}."
        );
    }

    /**
     * Cek status & progres batch recalculate SAW secara real-time.
     */
    public function status(?int $periodeId = null)
    {
        $batchId = null;
        if ($periodeId && $periodeId > 0) {
            $batchId = Cache::get("recalculate_saw_active_batch_{$periodeId}");
        }

        if (!$batchId) {
            $batchId = Cache::get('recalculate_saw_latest_batch');
        }

        if ($batchId) {
            $batch = Bus::findBatch($batchId);
            if ($batch) {
                return response()->json([
                    'has_batch'      => true,
                    'batch_id'       => $batch->id,
                    'batch_name'     => $batch->name,
                    'active'         => !$batch->finished() && !$batch->cancelled(),
                    'finished'       => $batch->finished(),
                    'cancelled'      => $batch->cancelled(),
                    'progress'       => $batch->progress(),
                    'total_jobs'     => $batch->totalJobs,
                    'pending_jobs'   => $batch->pendingJobs,
                    'processed_jobs' => $batch->processedJobs(),
                    'failed_jobs'    => $batch->failedJobs,
                ]);
            }
        }

        // Fallback jika batch sudah selesai atau tidak tersimpan di cache
        $sedangDihitungQuery = Pendaftaran::where('status', 'sedang_dihitung');
        if ($periodeId && $periodeId > 0) {
            $sedangDihitungQuery->where('periode_id', $periodeId);
        }
        $sedangDihitungCount = $sedangDihitungQuery->count();

        return response()->json([
            'has_batch'      => false,
            'active'         => $sedangDihitungCount > 0,
            'finished'       => $sedangDihitungCount === 0,
            'progress'       => $sedangDihitungCount > 0 ? 50 : 100,
            'total_jobs'     => 0,
            'processed_jobs' => 0,
            'failed_jobs'    => 0,
            'sedang_dihitung'=> $sedangDihitungCount,
        ]);
    }
}


