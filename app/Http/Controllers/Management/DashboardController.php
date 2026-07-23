<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Banding;
use App\Models\HasilUkt;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $periode = PeriodeSeleksi::where('status', 'aktif')->first();

        $stats = [
            'total_pendaftar'     => Pendaftaran::count(),
            'sudah_final'         => Pendaftaran::whereIn('status', ['final_ukt', 'selesai'])->count(),
            'menunggu_verifikasi' => Pendaftaran::where('status', 'menunggu_verifikasi')->count(),
            'banding_aktif'       => \App\Models\Banding::whereIn('status', ['diajukan', 'diproses'])->count(),
            'total_kip'           => Pendaftaran::whereRaw('"is_kip" IS TRUE')->count(),
            'total_non_kip'       => Pendaftaran::whereRaw('"is_kip" IS FALSE')->count(),
            'sudah_saw'           => \App\Models\HasilSaw::distinct('pendaftaran_id')->count(),
        ];

        // Distribusi golongan UKT
        $distribusiUkt = HasilUkt::selectRaw("golongan_final, COUNT(*) as total")
            ->groupBy('golongan_final')
            ->orderBy('golongan_final')
            ->get();

        // Distribusi per prodi
        $distribusiProdi = Pendaftaran::with('programStudi')
            ->selectRaw("program_studi_id, COUNT(*) as total")
            ->groupBy('program_studi_id')
            ->get();

        // $dataProdi = $distribusiProdi;
        $dataProdi = ProgramStudi::withCount('pendaftaran')
            ->get()
            ->filter(fn($p) => $p->pendaftaran_count > 0);


        // Statistik ekonomi
        $statsEkonomi = \App\Models\DataEkonomi::selectRaw('
        AVG(penghasilan_total) as rata_rata,
        MIN(penghasilan_total) as minimum,
        MAX(penghasilan_total) as maximum
    ')->first();

        // Trend per periode
        $trendPeriode  = PeriodeSeleksi::withCount('pendaftaran')
            ->orderBy('tahun')
            ->get();

        // $trendTahunan = $trendPeriode;
        $trendTahunan = PeriodeSeleksi::withCount('pendaftaran')
            ->orderBy('created_at')
            ->get();

        return view('management.dashboard', compact(
            'periode',
            'stats',
            'distribusiUkt',
            'distribusiProdi',
            'dataProdi',
            'statsEkonomi',
            'trendPeriode',
            'trendTahunan'
        ));
    }
}
