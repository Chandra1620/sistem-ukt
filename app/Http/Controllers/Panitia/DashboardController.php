<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\HasilUkt;
use App\Models\Banding;
use App\Models\PeriodeSeleksi;

class DashboardController extends Controller
{
    public function index()
    {
        $periode = PeriodeSeleksi::where('status', 'aktif')->first();

        $stats = [
            'hasil_awal'     => Pendaftaran::where('status', 'hasil_awal')->count(),
            'final_ukt'      => Pendaftaran::where('status', 'final_ukt')->count(),
            'banding_aktif'  => Banding::whereIn('status', ['diajukan', 'diproses'])->count(),
            'total_override' => HasilUkt::whereRaw('"is_override" IS TRUE')->count(),
        ];

        $menungguFinalisasi = Pendaftaran::where('status', 'hasil_awal')
            ->with(['user', 'programStudi', 'hasilUkt', 'hasilSaw'])
            ->latest()
            ->limit(10)
            ->get();

        $bandingTerbaru = Banding::whereIn('status', ['diajukan', 'diproses'])
            ->with(['pendaftaran.user', 'pendaftaran.programStudi', 'hasilUkt'])
            ->latest()
            ->limit(5)
            ->get();

        return view('panitia.dashboard', compact(
            'periode',
            'stats',
            'menungguFinalisasi',
            'bandingTerbaru'
        ));
    }
}
