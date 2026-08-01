<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\ProgramStudi;
use App\Models\PeriodeSeleksi;
use App\Models\HasilUkt;
use Illuminate\Support\Facades\Cache;

class StatistikProdiController extends Controller
{
    public function index()
    {
        $periode = PeriodeSeleksi::where('status', 'aktif')->first();

        $__cacheKey = 'mgmt.statistik-prodi.' . ($periode?->id ?? 'all');
        if ($__cached = Cache::get($__cacheKey)) {
            return view('management.statistik-prodi', $__cached);
        }

        $programStudi = ProgramStudi::aktif()
            ->withCount('pendaftaran')
            ->get();

        // Distribusi UKT per prodi dengan persentase yang benar
        $distribusiPerProdi = Pendaftaran::with(['programStudi', 'hasilUkt'])
            ->whereHas('hasilUkt')
            ->get()
            ->groupBy('program_studi_id')
            ->map(function ($group) {
                $prodi      = $group->first()->programStudi;
                $totalProdi = $group->count(); // total per prodi, bukan total semua

                $golongan = $group
                    ->groupBy(fn($p) => $p->hasilUkt->golongan_final ?? 0)
                    ->map(function ($g) use ($totalProdi) {
                        $jumlah = $g->count();
                        return [
                            'jumlah'     => $jumlah,
                            'persentase' => $totalProdi > 0
                                ? round(($jumlah / $totalProdi) * 100, 1)
                                : 0,
                        ];
                    });

                // Hitung sudah final
                $sudahFinal = $group->filter(
                    fn($p) => in_array($p->status, ['final_ukt', 'selesai'])
                )->count();

                return [
                    'nama'           => $prodi->nama ?? '-',
                    'jenjang'        => $prodi->jenjang ?? '-',
                    'total'          => $totalProdi,
                    'sudah_final'    => $sudahFinal,
                    'persen_final'   => $totalProdi > 0
                        ? round(($sudahFinal / $totalProdi) * 100, 1)
                        : 0,
                    'golongan'       => $golongan,
                    'total_kip'      => $golongan->has(0)
                        ? $golongan[0]['jumlah']
                        : 0,
                ];
            })->values();

        $__data = compact(
            'periode',
            'programStudi',
            'distribusiPerProdi'
        );
        Cache::put($__cacheKey, $__data, now()->addMinutes(5));

        return view('management.statistik-prodi', $__data);
    }
}
