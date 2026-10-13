<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Concerns\ResolvesPeriode;
use App\Http\Controllers\Controller;
use App\Models\DataEkonomi;
use App\Models\HasilUkt;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalitikController extends Controller
{
    use ResolvesPeriode;

    public function index()
    {
        // Periode terpilih dari dropdown (?periode=<id> | all). Default: semua periode.
        $rp        = $this->resolvePeriode();
        $periodeId = $rp['periodeId'];  // null = semua periode
        $periodes  = $rp['periodes'];
        $selected  = $rp['selected'];
        $periode   = $periodes->firstWhere('status', 'aktif');

        // Cache hasil analitik (HANYA data primitif/array agar aman di-unserialize;
        // object Eloquent yang di-cache bisa jadi incomplete object -> error 500).
        // Key per periode terpilih. "php artisan cache:clear" untuk refresh paksa.
        $__cacheKey = 'mgmt.analitik.' . $selected;

        $__data = Cache::remember($__cacheKey, now()->addMinutes(5), function () use ($periodeId) {
            // Filter via relasi "pendaftaran" (hasil_ukt & data_ekonomi tak punya periode_id).
            $filterViaPendaftaran = function ($q) use ($periodeId) {
                $q->where('status', '!=', 'draft');
                if ($periodeId) {
                    $q->where('periode_id', $periodeId);
                }
            };

            // Distribusi UKT per golongan (termasuk KIP), tanpa draft.
            $distribusiUkt = HasilUkt::selectRaw("golongan_final, COUNT(*) as total")
                ->whereHas('pendaftaran', $filterViaPendaftaran)
                ->groupBy('golongan_final')
                ->orderBy('golongan_final')
                ->get()
                ->map(fn($d) => [
                    'golongan_final' => $d->golongan_final,
                    'golongan'       => $d->golongan_final == 0 ? 'KIP' : 'Golongan ' . $d->golongan_final,
                    'total'          => (int) $d->total,
                    'is_kip'         => $d->golongan_final == 0,
                ])
                ->all();

            // Total pendaftar (tanpa draft, periode terpilih).
            $totalPendaftar = Pendaftaran::where('status', '!=', 'draft')
                ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
                ->count();

            // Statistik per prodi (tanpa draft, periode terpilih).
            $distribusiProdi = Pendaftaran::with('programStudi')
                ->where('status', '!=', 'draft')
                ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
                ->selectRaw("program_studi_id, COUNT(*) as total")
                ->groupBy('program_studi_id')
                ->get()
                ->map(fn($p) => [
                    'prodi' => $p->programStudi->nama ?? '-',
                    'total' => (int) $p->total,
                ])
                ->all();

            // Statistik ekonomi (periode terpilih, tanpa draft).
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

            // Heatmap wilayah (tanpa draft, periode terpilih).
            $heatmapWilayah = Pendaftaran::selectRaw("provinsi, COUNT(*) as total")
                ->where('status', '!=', 'draft')
                ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
                ->groupBy('provinsi')
                ->orderByDesc('total')
                ->get()
                ->map(fn($d) => [
                    'provinsi' => $d->provinsi,
                    'total'    => (int) $d->total,
                ])
                ->all();

            // Trend tahunan - SELALU lintas semua periode, draft tidak dihitung.
            $trendTahunan = PeriodeSeleksi::withCount([
                    'pendaftaran' => fn($q) => $q->where('status', '!=', 'draft'),
                ])
                ->orderBy('tahun')
                ->get()
                ->map(fn($p) => [
                    'nama'              => $p->nama,
                    'tahun'             => $p->tahun,
                    'status'            => $p->status,
                    'pendaftaran_count' => (int) $p->pendaftaran_count,
                ])
                ->all();

            // Distribusi pekerjaan orang tua: gabungan ayah + ibu (periode terpilih).
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
                ->values()
                ->all();

            return [
                'distribusiUkt'       => $distribusiUkt,
                'distribusiProdi'     => $distribusiProdi,
                'statsEkonomi'        => $statsEkonomi,
                'heatmapWilayah'      => $heatmapWilayah,
                'trendTahunan'        => $trendTahunan,
                'distribusiPekerjaan' => $distribusiPekerjaan,
                'totalPendaftar'      => $totalPendaftar,
            ];
        });

        // Rakit data untuk Blade. Item yang diakses via object (->) di-cast ke
        // stdClass; yang diakses via array ([]) dibiarkan array. Semua dibungkus
        // collection agar method seperti ->sum()/->count()/->pluck() tetap jalan.
        $viewData = [
            'periode'             => $periode,
            'periodes'            => $periodes,
            'selectedPeriode'     => $selected,
            'distribusiUkt'       => collect($__data['distribusiUkt']),                 // array items
            'distribusiProdi'     => collect($__data['distribusiProdi']),               // array items
            'dataProdi'           => collect($__data['distribusiProdi']),               // alias, array items
            'statsEkonomi'        => $__data['statsEkonomi'],                            // array
            'heatmapWilayah'      => collect($__data['heatmapWilayah'])->map(fn($r) => (object) $r), // object items
            'trendTahunan'        => collect($__data['trendTahunan'])->map(fn($r) => (object) $r),    // object items
            'distribusiPekerjaan' => collect($__data['distribusiPekerjaan']),           // array items
            'totalPendaftar'      => $__data['totalPendaftar'],
        ];

        return view('management.analitik', $viewData);
    }

    public function wilayahDetail(Request $request)
    {
        $provinsi = $request->query('provinsi');

        if (!$provinsi) {
            return response()->json([]);
        }

        // Periode dari query (?periode=<id> | all). Default semua periode.
        $periodeId = $this->resolvePeriodeId($request);

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
