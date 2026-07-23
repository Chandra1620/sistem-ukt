<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\PeriodeSeleksi;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
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
        // Filter berdasarkan periode yang dipilih atau periode aktif
        $periodeAktif = $request->filled('periode_id')
            ? PeriodeSeleksi::find($request->periode_id)
            : $this->getPeriodeAktif();

        $semua_periode = PeriodeSeleksi::orderByDesc('tahun')->get();

        $kriteria = $periodeAktif
            ? Kriteria::with(['subKriteria' => fn($q) => $q->orderBy('nilai')])
            ->where('periode_id', $periodeAktif->id)
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get()
            : collect();

        $kriteriaId    = $request->kriteria_id;
        $kriteriaAktif = $kriteriaId
            ? $kriteria->firstWhere('id', $kriteriaId)
            : $kriteria->first();

        return view('admin.sub-kriteria.index', compact(
            'kriteria',
            'kriteriaAktif',
            'periodeAktif',
            'semua_periode'
        ));
    }

    public function create(Request $request)
    {
        $periodeAktif = $this->getPeriodeAktif();

        $kriteria = $periodeAktif
            ? Kriteria::where('periode_id', $periodeAktif->id)
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get()
            : collect();

        $selectedKriteria = $request->kriteria_id
            ? Kriteria::find($request->kriteria_id)
            : null;

        return view('admin.sub-kriteria.create', compact(
            'kriteria',
            'selectedKriteria',
            'periodeAktif'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'label'       => 'required|string|max:255',
            'nilai'       => 'required|numeric|min:0',
            'range_min'   => 'nullable|string',
            'range_max'   => 'nullable|string',
        ]);

        $rangeMin = $request->range_min
            ? (float) str_replace(['.', ','], ['', '.'], $request->range_min)
            : null;
        $rangeMax = $request->range_max
            ? (float) str_replace(['.', ','], ['', '.'], $request->range_max)
            : null;

        $sub = SubKriteria::create([
            'kriteria_id' => $request->kriteria_id,
            'label'       => $request->label,
            'nilai'       => $request->nilai,
            'range_min'   => $rangeMin,
            'range_max'   => $rangeMax,
        ]);

        AuditLogService::log('buat_sub_kriteria', 'SubKriteria', $sub->id, null, $request->all());

        return redirect()->route('admin.sub-kriteria.index', ['kriteria_id' => $request->kriteria_id])
            ->with('success', 'Sub kriteria berhasil ditambahkan.');
    }

    public function edit(SubKriteria $subKriteria)
    {
        $periodeAktif = $this->getPeriodeAktif();

        $kriteria = $periodeAktif
            ? Kriteria::where('periode_id', $periodeAktif->id)
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get()
            : Kriteria::orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")->get();

        return view('admin.sub-kriteria.edit', compact('subKriteria', 'kriteria'));
    }

    public function update(Request $request, SubKriteria $subKriteria)
    {
        $request->validate([
            'kriteria_id' => 'required|exists:kriteria,id',
            'label'       => 'required|string|max:255',
            'nilai'       => 'required|numeric|min:0',
            'range_min'   => 'nullable|string',
            'range_max'   => 'nullable|string',
        ]);

        $rangeMin = $request->range_min
            ? (float) str_replace(['.', ','], ['', '.'], $request->range_min)
            : null;
        $rangeMax = $request->range_max
            ? (float) str_replace(['.', ','], ['', '.'], $request->range_max)
            : null;

        $dataLama = $subKriteria->toArray();
        $subKriteria->update([
            'kriteria_id' => $request->kriteria_id,
            'label'       => $request->label,
            'nilai'       => $request->nilai,
            'range_min'   => $rangeMin,
            'range_max'   => $rangeMax,
        ]);

        AuditLogService::log('ubah_sub_kriteria', 'SubKriteria', $subKriteria->id, $dataLama, $request->all());

        return redirect()->route('admin.sub-kriteria.index', ['kriteria_id' => $request->kriteria_id])
            ->with('success', 'Sub kriteria berhasil diperbarui.');
    }

    public function destroy(SubKriteria $subKriteria)
    {
        $kriteriaId = $subKriteria->kriteria_id;
        AuditLogService::log('hapus_sub_kriteria', 'SubKriteria', $subKriteria->id, $subKriteria->toArray(), null);
        $subKriteria->delete();

        return redirect()->route('admin.sub-kriteria.index', ['kriteria_id' => $kriteriaId])
            ->with('success', 'Sub kriteria berhasil dihapus.');
    }

    /**
     * Riwayat sub kriteria per periode
     */
    public function riwayat()
    {
        $riwayat = PeriodeSeleksi::with(['kriteria' => function ($q) {
            $q->withCount('subKriteria');
        }])
            ->orderByDesc('tahun')
            ->get()
            ->map(function ($p) {
                return [
                    'id'              => $p->id,
                    'nama'            => $p->nama,
                    'tahun'           => $p->tahun,
                    'status'          => $p->status,
                    'jumlah_kriteria' => $p->kriteria->count(),
                    'jumlah_sub'      => $p->kriteria->sum('sub_kriteria_count'),
                    'created_at'      => $p->created_at,
                    'updated_at'      => $p->updated_at,
                ];
            });

        return view('admin.sub-kriteria.riwayat', compact('riwayat'));
    }
}
