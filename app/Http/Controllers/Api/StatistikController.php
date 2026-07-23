<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\HasilUkt;
use App\Models\DataEkonomi;
use Illuminate\Support\Facades\DB;

class StatistikController extends Controller
{
    public function pendaftar()
    {
        $data = Pendaftaran::selectRaw("DATE(created_at) as tanggal, COUNT(*) as total")
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return response()->json($data);
    }

    public function distribusiUkt()
    {
        $data = HasilUkt::selectRaw("golongan_final, COUNT(*) as total")
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
        $data = Pendaftaran::with('programStudi')
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
        $data = DataEkonomi::selectRaw("
            AVG(penghasilan_total) as rata_rata,
            MIN(penghasilan_total) as minimum,
            MAX(penghasilan_total) as maximum,
            COUNT(*) as total
        ")->first();

        // Median manual
        $median = 0;
        $count  = DataEkonomi::count();
        if ($count > 0) {
            $sorted = DataEkonomi::orderBy('penghasilan_total')
                ->pluck('penghasilan_total');
            $mid    = (int) floor($count / 2);
            $median = $count % 2 === 0
                ? ($sorted[$mid - 1] + $sorted[$mid]) / 2
                : $sorted[$mid];
        }

        return response()->json([
            'rata_rata' => round($data->rata_rata ?? 0),
            'median'    => round($median),
            'minimum'   => $data->minimum ?? 0,
            'maximum'   => $data->maximum ?? 0,
            'total'     => $data->total ?? 0,
        ]);
    }
}
