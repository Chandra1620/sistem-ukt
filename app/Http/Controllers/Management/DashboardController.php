<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Concerns\ResolvesPeriode;
use App\Http\Controllers\Controller;
use App\Models\Banding;
use App\Models\HasilUkt;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    use ResolvesPeriode;

    public function index()
    {
        // Periode terpilih dari dropdown (?periode=<id> | all). Default: semua periode.
        $rp        = $this->resolvePeriode();
        $periodeId = $rp['periodeId'];      // null = semua periode
        $periodes  = $rp['periodes'];       // untuk dropdown
        $selected  = $rp['selected'];       // 'all' atau id periode

        // Periode aktif (khusus info, tidak dipakai untuk filter).
        $periode = $periodes->firstWhere('status', 'aktif');

        // -------------------------------------------------------------------
        // PERBAIKAN BUG 500 (incomplete object saat unserialize):
        // Jangan cache object Eloquent. Cache hanya data primitif/array yang
        // aman di-serialize. Cache di-key per periode terpilih.
        // Refresh paksa: "php artisan cache:clear".
        // -------------------------------------------------------------------
        $__cacheKey = 'mgmt.dashboard.' . $selected;

        $__data = Cache::remember($__cacheKey, now()->addMinutes(5), function () use ($periodeId) {
            // Helper filter periode via relasi pendaftaran (hasil_ukt, banding, data_ekonomi).
            $viaPendaftaran = function ($q) use ($periodeId) {
                if ($periodeId) {
                    $q->where('periode_id', $periodeId);
                }
            };

            $stats = [
                'total_pendaftar'     => Pendaftaran::when($periodeId, fn($q) => $q->where('periode_id', $periodeId))->count(),
                'sudah_final'         => Pendaftaran::whereIn('status', ['final_ukt', 'selesai'])
                    ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))->count(),
                'menunggu_verifikasi' => Pendaftaran::where('status', 'menunggu_verifikasi')
                    ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))->count(),
                'banding_aktif'       => Banding::whereIn('status', ['diajukan', 'diproses'])
                    ->whereHas('pendaftaran', $viaPendaftaran)->count(),
                'total_kip'           => Pendaftaran::whereRaw('"is_kip" IS TRUE')
                    ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))->count(),
                'total_non_kip'       => Pendaftaran::whereRaw('"is_kip" IS FALSE')
                    ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))->count(),
                'sudah_saw'           => \App\Models\HasilSaw::when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
                    ->distinct('pendaftaran_id')->count('pendaftaran_id'),
            ];

            // Distribusi golongan UKT -> array biasa
            $distribusiUkt = HasilUkt::selectRaw("golongan_final, COUNT(*) as total")
                ->whereHas('pendaftaran', $viaPendaftaran)
                ->groupBy('golongan_final')
                ->orderBy('golongan_final')
                ->get()
                ->map(fn($r) => [
                    'golongan_final' => $r->golongan_final,
                    'total'          => (int) $r->total,
                ])
                ->all();

            // Distribusi per prodi -> array biasa
            $distribusiProdi = Pendaftaran::with('programStudi')
                ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
                ->selectRaw("program_studi_id, COUNT(*) as total")
                ->groupBy('program_studi_id')
                ->get()
                ->map(fn($r) => [
                    'program_studi_id' => $r->program_studi_id,
                    'prodi'            => $r->programStudi->nama ?? null,
                    'total'            => (int) $r->total,
                ])
                ->all();

            // Jumlah prodi yang punya pendaftar (pada periode terpilih).
            $dataProdiCount = ProgramStudi::withCount([
                    'pendaftaran' => fn($q) => $q->when($periodeId, fn($qq) => $qq->where('periode_id', $periodeId)),
                ])
                ->get()
                ->filter(fn($p) => $p->pendaftaran_count > 0)
                ->count();

            // Statistik ekonomi -> array (di-cast ke object saat dikirim ke view).
            $statsEkonomi = \App\Models\DataEkonomi::whereHas('pendaftaran', $viaPendaftaran)
                ->selectRaw('
                    AVG(penghasilan_total) as rata_rata,
                    MIN(penghasilan_total) as minimum,
                    MAX(penghasilan_total) as maximum
                ')->first();
            $statsEkonomi = ($statsEkonomi && $statsEkonomi->rata_rata !== null) ? [
                'rata_rata' => (float) $statsEkonomi->rata_rata,
                'minimum'   => (float) $statsEkonomi->minimum,
                'maximum'   => (float) $statsEkonomi->maximum,
            ] : null;

            // Trend per periode & trend tahunan: SELALU lintas semua periode
            // (memang grafik pembanding antar periode), draft diabaikan.
            $trendPeriode = PeriodeSeleksi::withCount([
                    'pendaftaran' => fn($q) => $q->where('status', '!=', 'draft'),
                ])
                ->orderBy('tahun')
                ->get()
                ->map(fn($p) => [
                    'nama'              => $p->nama,
                    'tahun'             => $p->tahun,
                    'pendaftaran_count' => (int) $p->pendaftaran_count,
                ])
                ->all();

            $trendTahunan = PeriodeSeleksi::withCount([
                    'pendaftaran' => fn($q) => $q->where('status', '!=', 'draft'),
                ])
                ->orderBy('created_at')
                ->get()
                ->map(fn($p) => [
                    'nama'              => $p->nama,
                    'pendaftaran_count' => (int) $p->pendaftaran_count,
                ])
                ->all();

            return [
                'stats'           => $stats,
                'distribusiUkt'   => $distribusiUkt,
                'distribusiProdi' => $distribusiProdi,
                'dataProdiCount'  => $dataProdiCount,
                'statsEkonomi'    => $statsEkonomi,
                'trendPeriode'    => $trendPeriode,
                'trendTahunan'    => $trendTahunan,
            ];
        });

        // Bentuk data untuk Blade (cast balik ke object/collection agar
        // sintaks Blade lama tetap jalan: $statsEkonomi->rata_rata, $trendTahunan->pluck()).
        $viewData = [
            'periode'         => $periode,
            'periodes'        => $periodes,
            'selectedPeriode' => $selected,
            'stats'           => $__data['stats'],
            'distribusiUkt'   => collect($__data['distribusiUkt']),
            'distribusiProdi' => collect($__data['distribusiProdi']),
            'dataProdiCount'  => $__data['dataProdiCount'],
            'statsEkonomi'    => $__data['statsEkonomi'] ? (object) $__data['statsEkonomi'] : null,
            'trendPeriode'    => collect($__data['trendPeriode']),
            'trendTahunan'    => collect(array_map(fn($r) => (object) $r, $__data['trendTahunan'])),
        ];

        return view('management.dashboard', $viewData);
    }
}
