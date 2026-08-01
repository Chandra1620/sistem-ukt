<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\GolonganUkt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProyeksiPendapatanController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif  = PeriodeSeleksi::where('status', 'aktif')->first();
        $daftarPeriode = PeriodeSeleksi::orderByDesc('tahun')->get();
        $periodeId     = $request->input('periode_id');

        $__cacheKey = 'mgmt.proyeksi.' . ($periodeId ?: 'all');
        if ($__cached = Cache::get($__cacheKey)) {
            return view('management.proyeksi-pendapatan', $__cached);
        }

        // UKT "penuh" sebagai acuan subsidi = nominal golongan tertinggi
        $uktPenuh = (float) (GolonganUkt::max('nominal') ?? 0);

        // Hanya pendaftar yang sudah punya hasil UKT (golongan ditetapkan)
        $query = Pendaftaran::with(['hasilUkt', 'programStudi'])
            ->whereHas('hasilUkt');

        if (!empty($periodeId)) {
            $query->where('periode_id', $periodeId);
        }

        $pendaftaran = $query->get();

        $totalMahasiswa  = $pendaftaran->count();
        $totalPendapatan = 0.0;
        $totalSubsidi    = 0.0;
        $totalKip        = 0;

        foreach ($pendaftaran as $p) {
            $nominal = (float) ($p->hasilUkt->nominal_final ?? 0);
            $totalPendapatan += $nominal;

            $subsidi = $uktPenuh - $nominal;
            if ($subsidi > 0) {
                $totalSubsidi += $subsidi;
            }

            if ($p->is_kip) {
                $totalKip++;
            }
        }

        $rataUkt         = $totalMahasiswa > 0 ? $totalPendapatan / $totalMahasiswa : 0;
        $potensiMaksimum = $uktPenuh * $totalMahasiswa;
        $persenSubsidi   = $potensiMaksimum > 0
            ? round(($totalSubsidi / $potensiMaksimum) * 100, 1)
            : 0;

        // Rincian per program studi
        $perProdi = $pendaftaran
            ->groupBy('program_studi_id')
            ->map(function ($grp) {
                $prodi  = $grp->first()->programStudi;
                $jumlah = $grp->count();
                $total  = $grp->sum(fn($p) => (float) ($p->hasilUkt->nominal_final ?? 0));

                return [
                    'nama'    => $prodi->nama ?? '-',
                    'jenjang' => $prodi->jenjang ?? '-',
                    'jumlah'  => $jumlah,
                    'total'   => $total,
                    'rata'    => $jumlah > 0 ? $total / $jumlah : 0,
                ];
            })
            ->sortByDesc('total')
            ->values();

        // Rincian per golongan (golongan 0 = KIP)
        $perGolongan = $pendaftaran
            ->groupBy(fn($p) => (int) ($p->hasilUkt->golongan_final ?? 0))
            ->map(function ($grp, $gol) {
                return [
                    'golongan' => (int) $gol,
                    'jumlah'   => $grp->count(),
                    'total'    => $grp->sum(fn($p) => (float) ($p->hasilUkt->nominal_final ?? 0)),
                ];
            })
            ->sortBy('golongan')
            ->values();

        // Data siap-pakai untuk chart
        $prodiChart = [
            'labels' => $perProdi->take(10)->pluck('nama')->values(),
            'totals' => $perProdi->take(10)->pluck('total')->map(fn($v) => round($v))->values(),
        ];
        $golonganChart = [
            'labels' => $perGolongan
                ->map(fn($g) => $g['golongan'] === 0 ? 'KIP' : 'Golongan ' . $g['golongan'])
                ->values(),
            'totals' => $perGolongan->pluck('total')->map(fn($v) => round($v))->values(),
        ];

        $__data = compact(
            'periodeAktif',
            'daftarPeriode',
            'periodeId',
            'uktPenuh',
            'totalMahasiswa',
            'totalPendapatan',
            'totalSubsidi',
            'totalKip',
            'rataUkt',
            'potensiMaksimum',
            'persenSubsidi',
            'perProdi',
            'perGolongan',
            'prodiChart',
            'golonganChart'
        );
        Cache::put($__cacheKey, $__data, now()->addMinutes(5));

        return view('management.proyeksi-pendapatan', $__data);
    }
}
