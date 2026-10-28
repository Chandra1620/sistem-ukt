<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\ProgramStudi;
use App\Models\PeriodeSeleksi;
use App\Models\HasilUkt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        // Distribusi UKT & KIP per prodi dihitung langsung via agregasi SQL (hemat memori & performa tinggi)
        $distribusiPerProdi = DB::table('pendaftaran as p')
            ->join('program_studi as ps', 'ps.id', '=', 'p.program_studi_id')
            ->join('hasil_ukt as hu', 'hu.pendaftaran_id', '=', 'p.id')
            ->selectRaw("
                ps.id as program_studi_id,
                ps.nama,
                ps.jenjang,
                COALESCE(hu.golongan_final, 0) as golongan_final,
                COUNT(p.id) as total_golongan,
                COUNT(CASE WHEN p.status IN ('final_ukt', 'selesai') THEN 1 END) as final_golongan
            ")
            ->groupBy('ps.id', 'ps.nama', 'ps.jenjang', DB::raw('COALESCE(hu.golongan_final, 0)'))
            ->get()
            ->groupBy('program_studi_id')
            ->map(function ($group) {
                $first = $group->first();
                $totalProdi = (int) $group->sum('total_golongan');
                $sudahFinal = (int) $group->sum('final_golongan');

                $golongan = $group->keyBy('golongan_final')->map(function ($r) use ($totalProdi) {
                    $jumlah = (int) $r->total_golongan;
                    return [
                        'jumlah'     => $jumlah,
                        'persentase' => $totalProdi > 0
                            ? round(($jumlah / $totalProdi) * 100, 1)
                            : 0,
                    ];
                });

                return [
                    'nama'         => $first->nama ?? '-',
                    'jenjang'      => $first->jenjang ?? '-',
                    'total'        => $totalProdi,
                    'sudah_final'  => $sudahFinal,
                    'persen_final' => $totalProdi > 0
                        ? round(($sudahFinal / $totalProdi) * 100, 1)
                        : 0,
                    'golongan'     => $golongan,
                    'total_kip'    => $golongan->has(0)
                        ? $golongan[0]['jumlah']
                        : 0,
                ];
            })
            ->values();

        $__data = compact(
            'periode',
            'programStudi',
            'distribusiPerProdi'
        );
        Cache::put($__cacheKey, $__data, now()->addMinutes(5));

        return view('management.statistik-prodi', $__data);
    }
}
