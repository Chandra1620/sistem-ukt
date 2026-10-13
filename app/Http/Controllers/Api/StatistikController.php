<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesPeriode;
use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\HasilUkt;
use App\Models\DataEkonomi;
use App\Models\PeriodeSeleksi;

class StatistikController extends Controller
{
    use ResolvesPeriode;

    public function pendaftar()
    {
        // Periode dari query (?periode=<id> | all). Default semua periode.
        $periodeId = $this->resolvePeriodeId();

        $data = Pendaftaran::selectRaw("DATE(created_at) as tanggal, COUNT(*) as total")
            ->where('status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return response()->json($data);
    }

    public function distribusiUkt()
    {
        $periodeId = $this->resolvePeriodeId();

        $data = HasilUkt::selectRaw("golongan_final, COUNT(*) as total")
            ->whereHas('pendaftaran', function ($q) use ($periodeId) {
                $q->where('status', '!=', 'draft');
                if ($periodeId) {
                    $q->where('periode_id', $periodeId);
                }
            })
            ->groupBy('golongan_final')
            ->orderBy('golongan_final')
            ->get()
            ->map(fn($d) => [
                'golongan' => $d->golongan_final == 0 ? 'KIP' : 'Golongan ' . $d->golongan_final,
                'total'    => $d->total,
            ]);

        return response()->json($data);
    }

    public function perProdi()
    {
        $periodeId = $this->resolvePeriodeId();

        $data = Pendaftaran::with('programStudi')
            ->where('status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->selectRaw("program_studi_id, COUNT(*) as total")
            ->groupBy('program_studi_id')
            ->get()
            ->map(fn($p) => [
                'prodi' => $p->programStudi->nama ?? '-',
                'total' => $p->total,
            ]);

        return response()->json($data);
    }

    public function ekonomi()
    {
        $periodeId = $this->resolvePeriodeId();

        $ekonomiQuery = DataEkonomi::whereHas('pendaftaran', function ($q) use ($periodeId) {
            $q->where('status', '!=', 'draft');
            if ($periodeId) {
                $q->where('periode_id', $periodeId);
            }
        });

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

        return response()->json([
            'rata_rata' => round((float) ((clone $ekonomiQuery)->avg('penghasilan_total') ?? 0)),
            'median'    => round($median),
            'minimum'   => (float) ((clone $ekonomiQuery)->min('penghasilan_total') ?? 0),
            'maximum'   => (float) ((clone $ekonomiQuery)->max('penghasilan_total') ?? 0),
            'total'     => $count,
        ]);
    }
}
