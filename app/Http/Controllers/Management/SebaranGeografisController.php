<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SebaranGeografisController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif  = PeriodeSeleksi::where('status', 'aktif')->first();
        $daftarPeriode = PeriodeSeleksi::orderByDesc('tahun')->get();
        $periodeId     = $request->input('periode_id');

        $__cacheKey = 'mgmt.sebaran.' . ($periodeId ?: 'all');
        if ($__cached = Cache::get($__cacheKey)) {
            return view('management.sebaran-geografis', $__cached);
        }

        $pendaftaran = Pendaftaran::with(['dataEkonomi', 'hasilUkt'])
            ->where('status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->get();

        $totalMahasiswa = $pendaftaran->count();

        // Rekap per provinsi (dengan dimensi ekonomi & UKT)
        $perProvinsi = $pendaftaran
            ->groupBy(fn($p) => trim((string) $p->provinsi) ?: 'Tidak Diketahui')
            ->map(function ($grp, $prov) {
                $withEkonomi = $grp->filter(fn($p) => $p->dataEkonomi);
                $withUkt     = $grp->filter(fn($p) => $p->hasilUkt);

                return [
                    'provinsi'    => $prov,
                    'jumlah'      => $grp->count(),
                    'rata_income' => $withEkonomi->count()
                        ? (float) $withEkonomi->avg(fn($p) => (float) $p->dataEkonomi->penghasilan_total)
                        : 0.0,
                    'rata_ukt'    => $withUkt->count()
                        ? (float) $withUkt->avg(fn($p) => (float) $p->hasilUkt->nominal_final)
                        : 0.0,
                    'total_ukt'   => (float) $withUkt->sum(fn($p) => (float) $p->hasilUkt->nominal_final),
                    'kip'         => $grp->filter(fn($p) => $p->is_kip)->count(),
                ];
            })
            ->sortByDesc('jumlah')
            ->values();

        // Rekap per kota/kabupaten (asal_daerah)
        $perKota = $pendaftaran
            ->groupBy(fn($p) => trim((string) $p->asal_daerah) ?: 'Tidak Diketahui')
            ->map(fn($grp, $k) => ['daerah' => $k, 'jumlah' => $grp->count()])
            ->sortByDesc('jumlah')
            ->take(15)
            ->values();

        $totalProvinsi     = $perProvinsi->count();
        $provinsiTerbanyak = $perProvinsi->first();
        $kotaTerbanyak     = $perKota->first();

        // Data chart
        $treemapData = $perProvinsi->map(fn($p) => ['x' => $p['provinsi'], 'y' => $p['jumlah']])->values();
        $barTop = [
            'labels' => $perProvinsi->take(10)->pluck('provinsi')->values(),
            'data'   => $perProvinsi->take(10)->pluck('jumlah')->values(),
        ];

        $__data = compact(
            'periodeAktif',
            'daftarPeriode',
            'periodeId',
            'perProvinsi',
            'perKota',
            'totalProvinsi',
            'totalMahasiswa',
            'provinsiTerbanyak',
            'kotaTerbanyak',
            'treemapData',
            'barTop'
        );
        Cache::put($__cacheKey, $__data, now()->addMinutes(5));

        return view('management.sebaran-geografis', $__data);
    }
}
