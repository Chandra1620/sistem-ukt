<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\PeriodeSeleksi;
use App\Models\Pendaftaran;
use App\Models\HasilUkt;

class TrendTahunanController extends Controller
{
    public function index()
    {
        $periodeList = PeriodeSeleksi::withCount('pendaftaran')
            ->orderBy('tahun')
            ->orderBy('id')
            ->get();

        $periodeIds = $periodeList->pluck('id');

        // Jika tidak ada periode, langsung return view dengan data kosong
        if ($periodeIds->isEmpty()) {
            return view('management.trend-tahunan', [
                'periodeList' => $periodeList,
                'trendData' => collect([])
            ]);
        }

        // Hitung final count per periode
        $finalCounts = Pendaftaran::whereIn('periode_id', $periodeIds)
            ->whereIn('status', ['final_ukt', 'selesai'])
            ->selectRaw('periode_id, COUNT(*) as total')
            ->groupBy('periode_id')
            ->pluck('total', 'periode_id');

        // Hitung distribusi per periode (tanpa whereHas yang redundant)
        $distribusiData = HasilUkt::join('pendaftaran', 'hasil_ukt.pendaftaran_id', '=', 'pendaftaran.id')
            ->whereIn('pendaftaran.periode_id', $periodeIds)
            ->selectRaw('pendaftaran.periode_id, hasil_ukt.golongan_final, COUNT(*) as total')
            ->groupBy('pendaftaran.periode_id', 'hasil_ukt.golongan_final')
            ->orderBy('pendaftaran.periode_id')
            ->orderBy('hasil_ukt.golongan_final')
            ->get()
            ->groupBy('periode_id');

        $trendData = $periodeList->map(function ($p) use ($finalCounts, $distribusiData) {
            return [
                'periode'    => $p->nama,
                'tahun'      => $p->tahun,
                'status'     => $p->status,
                'total'      => $p->pendaftaran_count,
                'final'      => $finalCounts[$p->id] ?? 0,
                'distribusi' => $distribusiData[$p->id] ?? collect([]),
            ];
        });

        return view('management.trend-tahunan', compact('periodeList', 'trendData'));
    }
}
