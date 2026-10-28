<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\PeriodeSeleksi;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class KriteriaController extends Controller
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
            ? Kriteria::with('periode')
            ->where('periode_id', $periodeAktif->id)
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get()
            : collect();

        $totalBobot = $kriteria->sum('bobot');

        return view('admin.kriteria.index', compact(
            'kriteria',
            'periodeAktif',
            'semua_periode',
            'totalBobot'
        ));
    }

    public function create()
    {
        $periodeAktif    = $this->getPeriodeAktif();
        $periode         = PeriodeSeleksi::orderByDesc('tahun')->get();
        $bobotPerPeriode = Kriteria::selectRaw('periode_id, sum(bobot) as total')
            ->groupBy('periode_id')
            ->pluck('total', 'periode_id')
            ->map(fn($v) => round((float) $v, 2));

        return view('admin.kriteria.create', compact('periode', 'periodeAktif', 'bobotPerPeriode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'kode'       => 'required|string|max:10',
            'bobot'      => 'required|numeric|min:0|max:100',
            'tipe'       => 'required|in:cost,benefit',
            'periode_id' => 'required|exists:periode_seleksi,id',
        ]);

        // Hitung total bobot yang sudah ada pada periode_id yang dipilih
        $totalBobotAda = round((float) Kriteria::where('periode_id', $request->periode_id)->sum('bobot'), 2);
        $bobotBaru     = round((float) $request->bobot, 2);
        $sisaTersedia  = round(max(0, 100 - $totalBobotAda), 2);
        $totalSetelah  = round($totalBobotAda + $bobotBaru, 2);

        if ($totalSetelah > 100.00) {
            return back()->withInput()->with('error', "Total bobot kriteria pada periode ini tidak boleh melebihi 100% (total saat ini: {$totalBobotAda}%, input: {$bobotBaru}% = {$totalSetelah}%). Sisa bobot yang tersedia: {$sisaTersedia}%.");
        }

        // Cegah kode duplikat dalam satu periode
        $sudahAda = Kriteria::where('periode_id', $request->periode_id)
            ->where('kode', strtoupper($request->kode))
            ->exists();

        if ($sudahAda) {
            return back()->withInput()
                ->with('error', 'Kode kriteria ' . strtoupper($request->kode) . ' sudah ada pada periode ini.');
        }

        $kriteria = Kriteria::create([
            'nama'       => $request->nama,
            'kode'       => strtoupper($request->kode),
            'bobot'      => $request->bobot,
            'tipe'       => $request->tipe,
            'periode_id' => $request->periode_id,
        ]);

        AuditLogService::log('buat_kriteria', 'Kriteria', $kriteria->id, null, $request->all());

        return redirect()->route('admin.kriteria.index', ['periode_id' => $request->periode_id])
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function show(Kriteria $kriteria)
    {
        $kriteria->load('subKriteria');
        return view('admin.kriteria.show', compact('kriteria'));
    }

    public function edit(Kriteria $kriteria)
    {
        $periode = PeriodeSeleksi::orderByDesc('tahun')->get();
        $totalBobotLain = round((float) Kriteria::where('periode_id', $kriteria->periode_id)
            ->where('id', '!=', $kriteria->id)
            ->sum('bobot'), 2);
        $sisaTersedia = round(max(0, 100 - $totalBobotLain), 2);
        $totalSaatIni = round((float) Kriteria::where('periode_id', $kriteria->periode_id)->sum('bobot'), 2);

        return view('admin.kriteria.edit', compact(
            'kriteria',
            'periode',
            'totalBobotLain',
            'sisaTersedia',
            'totalSaatIni'
        ));
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        if (!$request->filled('periode_id')) {
            $request->merge(['periode_id' => $kriteria->periode_id]);
        }

        $request->validate([
            'nama'       => 'required|string|max:255',
            'kode'       => 'required|string|max:10',
            'bobot'      => 'required|numeric|min:0|max:100',
            'tipe'       => 'required|in:cost,benefit',
            'periode_id' => 'required|exists:periode_seleksi,id',
        ]);

        // Hitung total bobot kriteria lain dalam periode yang sama
        $totalBobotLain = round((float) Kriteria::where('periode_id', $request->periode_id)
            ->where('id', '!=', $kriteria->id)
            ->sum('bobot'), 2);
        $bobotBaru    = round((float) $request->bobot, 2);
        $sisaTersedia = round(max(0, 100 - $totalBobotLain), 2);
        $totalSetelah = round($totalBobotLain + $bobotBaru, 2);

        if ($totalSetelah > 100.00) {
            return back()->withInput()->with('error', "Total bobot kriteria pada periode ini tidak boleh melebihi 100% (total kriteria lain: {$totalBobotLain}%, input: {$bobotBaru}% = {$totalSetelah}%). Sisa bobot yang tersedia: {$sisaTersedia}%.");
        }

        // Cegah kode duplikat dalam periode yang sama (kecuali diri sendiri)
        $sudahAda = Kriteria::where('periode_id', $request->periode_id)
            ->where('kode', strtoupper($request->kode))
            ->where('id', '!=', $kriteria->id)
            ->exists();

        if ($sudahAda) {
            return back()->withInput()
                ->with('error', 'Kode kriteria ' . strtoupper($request->kode) . ' sudah ada pada periode ini.');
        }

        $dataLama = $kriteria->toArray();
        $kriteria->update([
            'nama'       => $request->nama,
            'kode'       => strtoupper($request->kode),
            'bobot'      => $request->bobot,
            'tipe'       => $request->tipe,
            'periode_id' => $request->periode_id,
        ]);

        AuditLogService::log('ubah_kriteria', 'Kriteria', $kriteria->id, $dataLama, $request->all());

        return redirect()->route('admin.kriteria.index', ['periode_id' => $request->periode_id])
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria)
    {
        if ($kriteria->subKriteria()->count() > 0) {
            return back()->with('error', 'Kriteria tidak dapat dihapus karena masih memiliki sub kriteria.');
        }

        $periodeId = $kriteria->periode_id;
        AuditLogService::log('hapus_kriteria', 'Kriteria', $kriteria->id, $kriteria->toArray(), null);
        $kriteria->delete();

        return redirect()->route('admin.kriteria.index', ['periode_id' => $periodeId])
            ->with('success', 'Kriteria berhasil dihapus.');
    }

    /**
     * Riwayat kriteria per periode
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
                    'total_bobot'     => $p->kriteria->sum('bobot'),
                    'created_at'      => $p->created_at,
                    'updated_at'      => $p->updated_at,
                ];
            });

        return view('admin.kriteria.riwayat', compact('riwayat'));
    }
}
