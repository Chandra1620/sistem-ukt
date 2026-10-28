<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ResolvesPeriode;
use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\HasilUkt;
use App\Models\DataEkonomi;
use App\Models\PeriodeSeleksi;
use Illuminate\Support\Facades\DB;

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

        $stats = DB::table('data_ekonomi as de')
            ->join('pendaftaran as p', 'p.id', '=', 'de.pendaftaran_id')
            ->where('p.status', '!=', 'draft')
            ->when($periodeId, fn($q) => $q->where('p.periode_id', $periodeId))
            ->selectRaw("
                COUNT(de.id) as count,
                COALESCE(AVG(de.penghasilan_total), 0) as rata_rata,
                COALESCE(MIN(de.penghasilan_total), 0) as minimum,
                COALESCE(MAX(de.penghasilan_total), 0) as maximum,
                COALESCE(PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY de.penghasilan_total), 0) as median
            ")
            ->first();

        $count = (int) ($stats->count ?? 0);

        return response()->json([
            'rata_rata' => round((float) ($stats->rata_rata ?? 0)),
            'median'    => round((float) ($stats->median ?? 0)),
            'minimum'   => (float) ($stats->minimum ?? 0),
            'maximum'   => (float) ($stats->maximum ?? 0),
            'total'     => $count,
        ]);
    }
}
