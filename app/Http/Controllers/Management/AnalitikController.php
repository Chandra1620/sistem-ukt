<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\DataEkonomi;
use App\Models\HasilUkt;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalitikController extends Controller
{
    public function index()
    {
        $periode   = PeriodeSeleksi::where('status', 'aktif')->first();
        $periodeId = $periode?->id;

        // Cache hasil analitik (data jarang berubah). Jalankan
        // "php artisan cache:clear" untuk memaksa refresh setelah impor/finalisasi.
        $__cacheKey = 'mgmt.analitik.' . ($periodeId ?? 'all');
        if ($__cached = Cache::get($__cacheKey)) {
            return view('management.analitik', $__cached);
        }

        // Filter dipakai ulang untuk query berbasis relasi "pendaftaran".
        // hasil_ukt & data_ekonomi tidak punya periode_id; keterkaitan
        // periode + status diambil lewat relasi pendaftaran.
        $filterViaPendaftaran = function ($q) use ($periodeId) {
            $q->where('status', '!=', 'draft');
            if ($periodeId) {
                $q->where('periode_id', $periodeId);
            }
        };

        // Distribusi UKT per golongan (termasuk KIP) - periode aktif, tanpa draft
        $distribusiUkt = HasilUkt::selectRaw("golongan_final, COUNT(*) as total")
            ->whereHas('pendaftaran', $filterViaPendaftaran)
            ->groupBy('golongan_final')
            ->orderBy('golongan_final')
            ->get()
            ->map(fn($d) => [
                'golongan_final' => $d->golongan_final,
                'golongan'       => $d->golongan_final == 0 ? 'KIP' : 'Golongan ' . $d->golongan_final,
                'total'          => $d->total,
                'is_kip'         => $d->golongan_final == 0,
            ]);

        // Total pendaftar (tanpa draft, periode aktif)
        $totalPendaftar = Pendaftaran::where('status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->count();

        // Statistik per prodi (tanpa draft, periode aktif)
        $distribusiProdi = Pendaftaran::with('programStudi')
            ->where('status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->selectRaw("program_studi_id, COUNT(*) as total")
            ->groupBy('program_studi_id')
            ->get()
            ->map(fn($p) => [
                'prodi' => $p->programStudi->nama ?? '-',
                'total' => $p->total,
            ]);

        $dataProdi = collect($distribusiProdi);

        // Statistik ekonomi (periode aktif, tanpa draft).
        // Cast eksplisit ke float: kolom decimal PostgreSQL dikembalikan
        // sebagai string sehingga aritmatika (median) bisa keliru.
        $ekonomiQuery = DataEkonomi::whereHas('pendaftaran', $filterViaPendaftaran);

        $count  = (clone $ekonomiQuery)->count();
        $median = 0.0;
        if ($count > 0) {
            $sorted = (clone $ekonomiQuery)
                ->orderBy('penghasilan_total')
                ->pluck('penghasilan_total')
                ->map(fn($v) => (float) $v)
                ->values();
            $mid    = intdiv($count, 2);
            $median = $count % 2 === 0
                ? (($sorted[$mid - 1] + $sorted[$mid]) / 2)
                : $sorted[$mid];
        }

        $statsEkonomi = [
            'rata_rata'       => (float) ((clone $ekonomiQuery)->avg('penghasilan_total') ?? 0),
            'median'          => (float) $median,
            'minimum'         => (float) ((clone $ekonomiQuery)->min('penghasilan_total') ?? 0),
            'maximum'         => (float) ((clone $ekonomiQuery)->max('penghasilan_total') ?? 0),
            'total_pendaftar' => $totalPendaftar,
        ];

        // Heatmap wilayah (tanpa draft, periode aktif)
        $heatmapWilayah = Pendaftaran::selectRaw("provinsi, COUNT(*) as total")
            ->where('status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->groupBy('provinsi')
            ->orderByDesc('total')
            ->get();

        // Trend tahunan - sengaja lintas semua periode, tapi draft tidak dihitung
        $trendTahunan = PeriodeSeleksi::withCount([
                'pendaftaran' => fn($q) => $q->where('status', '!=', 'draft'),
            ])
            ->orderBy('tahun')
            ->get();

        // Distribusi pekerjaan orang tua: gabungan ayah + ibu,
        // tapi tetap ditandai terpisah berapa ayah & berapa ibu.
        $pekerjaanAyah = DataEkonomi::selectRaw("pekerjaan_ayah as pekerjaan, COUNT(*) as total")
            ->whereHas('pendaftaran', $filterViaPendaftaran)
            ->whereNotNull('pekerjaan_ayah')
            ->where('pekerjaan_ayah', '!=', '')
            ->groupBy('pekerjaan_ayah')
            ->pluck('total', 'pekerjaan');

        $pekerjaanIbu = DataEkonomi::selectRaw("pekerjaan_ibu as pekerjaan, COUNT(*) as total")
            ->whereHas('pendaftaran', $filterViaPendaftaran)
            ->whereNotNull('pekerjaan_ibu')
            ->where('pekerjaan_ibu', '!=', '')
            ->groupBy('pekerjaan_ibu')
            ->pluck('total', 'pekerjaan');

        $distribusiPekerjaan = collect($pekerjaanAyah->keys())
            ->merge($pekerjaanIbu->keys())
            ->unique()
            ->map(function ($pekerjaan) use ($pekerjaanAyah, $pekerjaanIbu) {
                $ayah = (int) ($pekerjaanAyah[$pekerjaan] ?? 0);
                $ibu  = (int) ($pekerjaanIbu[$pekerjaan] ?? 0);
                return [
                    'pekerjaan' => $pekerjaan,
                    'ayah'      => $ayah,
                    'ibu'       => $ibu,
                    'total'     => $ayah + $ibu,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $__data = compact(
            'periode',
            'distribusiUkt',
            'distribusiProdi',
            'dataProdi',
            'statsEkonomi',
            'heatmapWilayah',
            'trendTahunan',
            'distribusiPekerjaan',
            'totalPendaftar'
        );
        Cache::put($__cacheKey, $__data, now()->addMinutes(5));

        return view('management.analitik', $__data);
    }

    public function wilayahDetail(Request $request)
    {
        $provinsi = $request->query('provinsi');

        if (!$provinsi) {
            return response()->json([]);
        }

        $periodeId = PeriodeSeleksi::where('status', 'aktif')->value('id');

        $data = Pendaftaran::selectRaw("asal_daerah, COUNT(*) as total")
            ->where('provinsi', $provinsi)
            ->where('status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
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
