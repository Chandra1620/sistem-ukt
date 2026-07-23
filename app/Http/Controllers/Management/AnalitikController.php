<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\DataEkonomi;
use App\Models\HasilUkt;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AnalitikController extends Controller
{
    public function index()
    {
        $periode = PeriodeSeleksi::where('status', 'aktif')->first();

        // Distribusi UKT per golongan
        // Distribusi UKT per golongan (termasuk KIP)
        $distribusiUkt = HasilUkt::selectRaw("golongan_final, COUNT(*) as total")
            ->groupBy('golongan_final')
            ->orderBy('golongan_final')
            ->get()
            ->map(fn($d) => [
                'golongan_final' => $d->golongan_final,
                'golongan'       => $d->golongan_final == 0 ? 'KIP' : 'Golongan ' . $d->golongan_final,
                'total'          => $d->total,
                'is_kip'         => $d->golongan_final == 0,
            ]);
        $totalPendaftar = Pendaftaran::count();

        // Statistik per prodi
        $distribusiProdi = Pendaftaran::with('programStudi')
            ->selectRaw("program_studi_id, COUNT(*) as total")
            ->groupBy('program_studi_id')
            ->get()
            ->map(fn($p) => [
                'prodi' => $p->programStudi->nama ?? '-',
                'total' => $p->total,
            ]);

        $dataProdi = collect($distribusiProdi);

        // Statistik ekonomi
        $count  = DataEkonomi::count();
        $median = 0;
        if ($count > 0) {
            $sorted = DataEkonomi::orderBy('penghasilan_total')
                ->pluck('penghasilan_total');
            $mid    = (int) floor($count / 2);
            $median = $count % 2 === 0
                ? ($sorted[$mid - 1] + $sorted[$mid]) / 2
                : $sorted[$mid];
        }

        $statsEkonomi = [
            'rata_rata'       => DataEkonomi::avg('penghasilan_total') ?? 0,
            'median'          => $median,
            'minimum'         => DataEkonomi::min('penghasilan_total') ?? 0,
            'maximum'         => DataEkonomi::max('penghasilan_total') ?? 0,
            'total_pendaftar' => Pendaftaran::count(),
        ];

        // Heatmap wilayah
        $heatmapWilayah = Pendaftaran::selectRaw("provinsi, COUNT(*) as total")
            ->groupBy('provinsi')
            ->orderByDesc('total')
            ->get();

        // Trend tahunan
        $trendTahunan = PeriodeSeleksi::withCount('pendaftaran')
            ->orderBy('tahun')
            ->get();

        // Distribusi pekerjaan orang tua
        $distribusiPekerjaan = DataEkonomi::selectRaw("pekerjaan_ayah, COUNT(*) as total")
            ->groupBy('pekerjaan_ayah')
            ->orderByDesc('total')
            ->get();

        return view('management.analitik', compact(
            'periode',
            'distribusiUkt',
            'distribusiProdi',
            'dataProdi',
            'statsEkonomi',
            'heatmapWilayah',
            'trendTahunan',
            'distribusiPekerjaan',
            'totalPendaftar'
        ));
    }

    public function wilayahDetail(\Illuminate\Http\Request $request)
    {
        $provinsi = $request->query('provinsi');

        if (!$provinsi) {
            return response()->json([]);
        }

        $data = Pendaftaran::selectRaw("asal_daerah, COUNT(*) as total")
            ->where('provinsi', $provinsi)
            ->whereNotNull('asal_daerah')
            ->where('asal_daerah', '!=', '')
            ->groupBy('asal_daerah')
            ->orderByDesc('total')
            ->limit(15)
            ->get()
            ->map(fn($d) => [
                'daerah' => $d->asal_daerah,
                'total'  => $d->total,
            ]);

        return response()->json($data);
    }
}
