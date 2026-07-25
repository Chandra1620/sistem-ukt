<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use Illuminate\Http\Request;

class AnalisisKeadilanController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif  = PeriodeSeleksi::where('status', 'aktif')->first();
        $daftarPeriode = PeriodeSeleksi::orderByDesc('tahun')->get();
        $periodeId     = $request->input('periode_id');

        // Hanya pendaftar yang punya data ekonomi DAN hasil UKT (bukan draft)
        $pendaftaran = Pendaftaran::with(['hasilUkt', 'dataEkonomi', 'programStudi'])
            ->whereHas('hasilUkt')
            ->whereHas('dataEkonomi')
            ->where('status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->get();

        // Datapoint bersih untuk analisis
        $items = $pendaftaran->map(function ($p) {
            return [
                'nama'     => $p->nama_lengkap ?? '-',
                'prodi'    => $p->programStudi->nama ?? '-',
                'income'   => (float) ($p->dataEkonomi->penghasilan_total ?? 0),
                'nominal'  => (float) ($p->hasilUkt->nominal_final ?? 0),
                'golongan' => (int) ($p->hasilUkt->golongan_final ?? 0),
                'is_kip'   => (bool) $p->is_kip,
            ];
        })->values();

        $n = $items->count();

        // Helper persentil pada koleksi angka terurut menaik
        $percentile = function ($sorted, $p) {
            $c = $sorted->count();
            if ($c === 0) return 0.0;
            if ($c === 1) return (float) $sorted[0];
            $idx = ($c - 1) * $p;
            $lo  = (int) floor($idx);
            $hi  = (int) ceil($idx);
            if ($lo === $hi) return (float) $sorted[$lo];
            return (float) $sorted[$lo] + ($idx - $lo) * ((float) $sorted[$hi] - (float) $sorted[$lo]);
        };

        // Korelasi Pearson: penghasilan vs nominal UKT
        // r > 0 berarti makin tinggi penghasilan makin tinggi UKT (adil)
        $r = 0.0;
        if ($n > 1) {
            $meanX = (float) $items->avg('income');
            $meanY = (float) $items->avg('nominal');
            $num = 0.0;
            $denX = 0.0;
            $denY = 0.0;
            foreach ($items as $it) {
                $dx = $it['income'] - $meanX;
                $dy = $it['nominal'] - $meanY;
                $num  += $dx * $dy;
                $denX += $dx * $dx;
                $denY += $dy * $dy;
            }
            $r = ($denX > 0 && $denY > 0) ? $num / sqrt($denX * $denY) : 0.0;
        }
        $r = round($r, 3);

        $skorKeadilan = (int) round(max(0, $r) * 100);
        if ($r >= 0.6) {
            $labelKeadilan = 'Sangat Adil';
            $warnaKeadilan = '#10b981';
        } elseif ($r >= 0.4) {
            $labelKeadilan = 'Cukup Adil';
            $warnaKeadilan = '#22c55e';
        } elseif ($r >= 0.2) {
            $labelKeadilan = 'Kurang Adil';
            $warnaKeadilan = '#f59e0b';
        } else {
            $labelKeadilan = 'Perlu Ditinjau';
            $warnaKeadilan = '#ef4444';
        }

        // Tingkatan ekonomi (kuintil berdasarkan penghasilan)
        $labelTier = ['Termiskin 20%', 'Menengah Bawah', 'Menengah', 'Menengah Atas', 'Terkaya 20%'];
        $perTier = collect();
        if ($n > 0) {
            $byIncome = $items->sortBy('income')->values();
            $tierSize = (int) max(1, ceil($n / 5));
            $byIncome->chunk($tierSize)->take(5)->values()->each(function ($grp, $i) use (&$perTier, $labelTier) {
                $perTier->push([
                    'label'         => $labelTier[$i] ?? ('Tingkat ' . ($i + 1)),
                    'jumlah'        => $grp->count(),
                    'rata_income'   => (float) $grp->avg('income'),
                    'rata_nominal'  => (float) $grp->avg('nominal'),
                    'rata_golongan' => round((float) $grp->avg('golongan'), 1),
                ]);
            });
        }

        // Ambang deteksi anomali
        $sortedIncome  = $items->pluck('income')->sort()->values();
        $sortedNominal = $items->pluck('nominal')->sort()->values();
        $q1Income      = $percentile($sortedIncome, 0.25);
        $q3Income      = $percentile($sortedIncome, 0.75);
        $medNominal    = $percentile($sortedNominal, 0.50);

        // Anomali A: ekonomi lemah (income <= Q1) tapi UKT tinggi (>= median), bukan KIP
        $uktTerlaluTinggi = $items
            ->filter(fn($it) => !$it['is_kip'] && $medNominal > 0 && $it['income'] <= $q1Income && $it['nominal'] >= $medNominal)
            ->sortByDesc('nominal')
            ->take(15)
            ->values();

        // Anomali B: ekonomi kuat (income >= Q3) tapi UKT rendah (<= median)
        $uktTerlaluRendah = $items
            ->filter(fn($it) => $q3Income > 0 && $it['income'] >= $q3Income && $it['nominal'] <= $medNominal)
            ->sortByDesc('income')
            ->take(15)
            ->values();

        // Data chart tingkatan
        $tierChart = [
            'labels'  => $perTier->pluck('label')->values(),
            'nominal' => $perTier->pluck('rata_nominal')->map(fn($v) => round($v))->values(),
            'income'  => $perTier->pluck('rata_income')->map(fn($v) => round($v))->values(),
        ];

        // Data scatter: penghasilan vs nominal UKT, dipisah normal vs anomali
        $anomaliKeys = $uktTerlaluTinggi->merge($uktTerlaluRendah)
            ->map(fn($it) => $it['nama'] . '|' . $it['income'] . '|' . $it['nominal']);
        $scatterNormal  = collect();
        $scatterAnomali = collect();
        foreach ($items as $it) {
            $key   = $it['nama'] . '|' . $it['income'] . '|' . $it['nominal'];
            $point = ['x' => round($it['income']), 'y' => round($it['nominal'])];
            if ($anomaliKeys->contains($key)) {
                $scatterAnomali->push($point);
            } else {
                $scatterNormal->push($point);
            }
        }

        return view('management.analisis-keadilan', compact(
            'periodeAktif',
            'daftarPeriode',
            'periodeId',
            'n',
            'r',
            'skorKeadilan',
            'labelKeadilan',
            'warnaKeadilan',
            'perTier',
            'tierChart',
            'q1Income',
            'q3Income',
            'medNominal',
            'uktTerlaluTinggi',
            'uktTerlaluRendah',
            'scatterNormal',
            'scatterAnomali'
        ));
    }
}
