<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\HasilUkt;
use App\Models\GolonganUkt;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;

class DistribusiUktController extends Controller
{
    public function index()
    {
        $periode = PeriodeSeleksi::where('status', 'aktif')->first();

        // Total seluruh mahasiswa yang sudah punya hasil UKT
        $totalFinalisasi = HasilUkt::count();

        // Distribusi raw dari database
        $distribusi = HasilUkt::selectRaw("golongan_final, COUNT(*) as total")
            ->groupBy('golongan_final')
            ->orderBy('golongan_final')
            ->get();

        $golongan = GolonganUkt::orderBy('golongan')->get();

        // === Data KIP (golongan 0) ===
        $kipData = $distribusi->firstWhere('golongan_final', 0);
        $totalKip = $kipData ? $kipData->total : 0;

        // === Data per golongan (1-8, dari master golongan_ukt) ===
        $dataGolongan = $golongan->map(function ($g) use ($distribusi, $totalFinalisasi) {
            $found = $distribusi->firstWhere('golongan_final', $g->golongan);
            $total = $found ? $found->total : 0;
            return [
                'golongan'    => $g->golongan,
                'label'       => 'Golongan ' . $g->golongan,
                'nominal'     => $g->nominal,
                'total'       => $total,
                'persentase'  => $totalFinalisasi > 0
                    ? round(($total / $totalFinalisasi) * 100, 1)
                    : 0,
                'is_kip'      => false,
            ];
        });

        // === Gabungkan KIP di urutan pertama ===
        $dataLengkap = collect([[
            'golongan'    => 0,
            'label'       => 'KIP',
            'nominal'     => null,
            'total'       => $totalKip,
            'persentase'  => $totalFinalisasi > 0
                ? round(($totalKip / $totalFinalisasi) * 100, 1)
                : 0,
            'is_kip'      => true,
        ]])->concat($dataGolongan);

        return view('management.distribusi-ukt', compact(
            'periode',
            'distribusi',
            'dataGolongan',
            'dataLengkap',
            'totalFinalisasi',
            'totalKip'
        ));
    }
}
