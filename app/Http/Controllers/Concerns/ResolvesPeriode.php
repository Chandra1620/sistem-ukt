<?php

namespace App\Http\Controllers\Concerns;

use App\Models\PeriodeSeleksi;
use Illuminate\Http\Request;

/**
 * Helper bersama untuk menentukan periode yang dipilih dari query string.
 *
 * Aturan:
 *  - ?periode=all  (atau kosong / tidak dikirim) => SEMUA periode (gabungan)
 *  - ?periode=<id>                                => periode spesifik tsb
 *
 * Default sengaja "semua periode" agar dashboard/analitik menampilkan data
 * seluruh periode, lalu bisa dipersempit lewat dropdown pemilih periode.
 */
trait ResolvesPeriode
{
    /**
     * @return array{selected:string|int, periodeId:?int, periode:?PeriodeSeleksi, periodes:\Illuminate\Support\Collection, isAll:bool}
     */
    protected function resolvePeriode(?Request $request = null): array
    {
        $request ??= request();
        $param = $request->query('periode');

        // Daftar periode untuk dropdown (tahun terbaru dulu).
        $periodes = PeriodeSeleksi::orderByDesc('tahun')->orderByDesc('id')->get();

        // Periode spesifik bila id valid.
        if (is_numeric($param)) {
            $periode = $periodes->firstWhere('id', (int) $param);
            if ($periode) {
                return [
                    'selected'  => (int) $param,
                    'periodeId' => (int) $param,
                    'periode'   => $periode,
                    'periodes'  => $periodes,
                    'isAll'     => false,
                ];
            }
        }

        // Default / "all": semua periode.
        return [
            'selected'  => 'all',
            'periodeId' => null,
            'periode'   => null,
            'periodes'  => $periodes,
            'isAll'     => true,
        ];
    }

    /**
     * Versi ringan untuk endpoint API: cukup kembalikan periodeId (null = semua).
     */
    protected function resolvePeriodeId(?Request $request = null): ?int
    {
        $request ??= request();
        $param = $request->query('periode');

        if (is_numeric($param)) {
            $id = (int) $param;
            if (PeriodeSeleksi::whereKey($id)->exists()) {
                return $id;
            }
        }

        // "all", kosong, atau tidak valid => semua periode.
        return null;
    }
}
