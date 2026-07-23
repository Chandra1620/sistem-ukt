<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\HasilSaw;
use App\Models\DetailSaw;
use App\Models\HasilUkt;
use App\Models\PeriodeSeleksi;
use App\Jobs\ProsesHitungSaw;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class RecalculateSawController extends Controller
{
    public function recalculate(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periode_seleksi,id',
        ]);

        $periode = PeriodeSeleksi::findOrFail($request->periode_id);

        // Ambil semua pendaftaran yang sudah diverifikasi ke atas
        $pendaftaranList = Pendaftaran::where('periode_id', $periode->id)
            ->whereIn('status', [
                'diverifikasi',
                'sedang_dihitung',
                'hasil_awal',
                'final_ukt',
                'selesai'
            ])
            ->get();

        if ($pendaftaranList->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang bisa dihitung ulang.');
        }

        // Hapus hasil SAW & UKT lama
        $ids = $pendaftaranList->pluck('id');

        DetailSaw::whereHas('hasilSaw', fn($q) => $q->whereIn('pendaftaran_id', $ids))->delete();
        HasilSaw::whereIn('pendaftaran_id', $ids)->delete();
        HasilUkt::whereIn('pendaftaran_id', $ids)->delete();

        // Reset status ke diverifikasi
        Pendaftaran::whereIn('id', $ids)
            ->whereIn('status', ['sedang_dihitung', 'hasil_awal', 'final_ukt', 'selesai'])
            ->update(['status' => 'diverifikasi']);

        // Dispatch ulang job SAW
        $count = 0;
        $gagal = 0;
        foreach ($pendaftaranList as $p) {
            $p->refresh();
            if (in_array($p->status, ['diverifikasi'])) {
                try {
                    ProsesHitungSaw::dispatchSync($p, false);
                    $count++;
                } catch (\Throwable $e) {
                    $gagal++;
                    \Illuminate\Support\Facades\Log::error(
                        "Recalculate SAW gagal untuk pendaftaran {$p->id}: " . $e->getMessage()
                    );
                }
            }
        }

        if ($count > 0) {
            app(\App\Services\SawService::class)->updateRankingPeriode($periode->id);
        }

        AuditLogService::log(
            'recalculate_saw',
            'PeriodeSeleksi',
            $periode->id,
            null,
            ['jumlah' => $count, 'periode' => $periode->nama]
        );

        return back()->with('success', "Perhitungan ulang SAW selesai untuk {$count} mahasiswa pada periode {$periode->nama}."
            . ($gagal > 0 ? " ({$gagal} gagal, cek log untuk detail.)" : ''));
    }
}
