<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\HasilUkt;
use App\Models\GolonganUkt;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DistribusiUktController extends Controller
{
    /**
     * Ambil periode aktif atau fallback ke periode terbaru
     */
    private function getPeriodeAktif(): ?PeriodeSeleksi
    {
        return PeriodeSeleksi::where('status', 'aktif')->first()
            ?? PeriodeSeleksi::orderByDesc('id')->first();
    }

    public function index(Request $request)
    {
        $daftarPeriode = PeriodeSeleksi::orderByDesc('tahun')->orderByDesc('id')->get();
        $periodeAktif  = $this->getPeriodeAktif();

        // Default: periode aktif. Hormati pilihan dropdown jika ada.
        $selectedParam = $request->query('periode_id') ?? $request->query('periode');
        $selectedPeriodeId = $selectedParam ?? ($periodeAktif ? (string) $periodeAktif->id : 'all');

        $periodeTerpilih = null;
        if ($selectedPeriodeId !== 'all') {
            $periodeTerpilih = $daftarPeriode->firstWhere('id', (int) $selectedPeriodeId) ?? $periodeAktif;
        }

        // Cache hasil distribusi dengan kunci menyertakan periode_id
        $cacheKey = 'mgmt.distribusi-ukt.' . $selectedPeriodeId;

        $cachedData = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($periodeTerpilih) {
            // Query agregasi tunggal: menghitung total dan nominal per golongan via relasi pendaftaran.periode_id
            $distribusiQuery = HasilUkt::query();
            if ($periodeTerpilih) {
                $distribusiQuery->whereHas('pendaftaran', function ($q) use ($periodeTerpilih) {
                    $q->where('periode_id', $periodeTerpilih->id);
                });
            }

            $distribusi = $distribusiQuery
                ->selectRaw("golongan_final, COUNT(*) as total, SUM(nominal_final) as total_nominal")
                ->groupBy('golongan_final')
                ->orderBy('golongan_final')
                ->get();

            $totalFinalisasi = (int) $distribusi->sum('total');
            $totalNominal    = (float) $distribusi->sum('total_nominal');

            $golongan = GolonganUkt::orderBy('golongan')->get();

            // === Data KIP (golongan 0) ===
            $kipData  = $distribusi->firstWhere('golongan_final', 0);
            $totalKip = $kipData ? (int) $kipData->total : 0;

            // === Data per golongan (1-8, dari master golongan_ukt) ===
            $dataGolongan = $golongan->map(function ($g) use ($distribusi, $totalFinalisasi) {
                $found = $distribusi->firstWhere('golongan_final', $g->golongan);
                $total = $found ? (int) $found->total : 0;
                return [
                    'golongan'    => $g->golongan,
                    'label'       => 'Golongan ' . $g->golongan,
                    'nominal'     => (float) $g->nominal,
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

            return [
                'distribusi'      => $distribusi->toArray(),
                'dataGolongan'    => $dataGolongan->toArray(),
                'dataLengkap'     => $dataLengkap->values()->toArray(),
                'totalFinalisasi' => $totalFinalisasi,
                'totalKip'        => $totalKip,
                'totalNominal'    => $totalNominal,
            ];
        });

        $periode         = $periodeTerpilih ?? $periodeAktif;
        $distribusi      = collect($cachedData['distribusi']);
        $dataGolongan    = collect($cachedData['dataGolongan']);
        $dataLengkap     = collect($cachedData['dataLengkap']);
        $totalFinalisasi = $cachedData['totalFinalisasi'];
        $totalKip        = $cachedData['totalKip'];
        $totalNominal    = $cachedData['totalNominal'];

        return view('management.distribusi-ukt', compact(
            'periode',
            'periodeTerpilih',
            'daftarPeriode',
            'selectedPeriodeId',
            'distribusi',
            'dataGolongan',
            'dataLengkap',
            'totalFinalisasi',
            'totalKip',
            'totalNominal'
        ));
    }
}
