<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akreditasi;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkreditasiController extends Controller
{
    public function index()
    {
        $akreditasi = Akreditasi::withCount('programStudi')
            ->orderBy('urutan')
            ->get();

        return view('admin.akreditasi.index', compact('akreditasi'));
    }

    public function create()
    {
        return view('admin.akreditasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100|unique:akreditasi,nama',
            'nominal_kip' => 'required|numeric|min:0',
            'keterangan'  => 'nullable|string',
            'urutan'      => 'required|integer|min:0',
        ], [
            'nama.unique' => 'Nama akreditasi sudah ada.',
        ]);

        $aktif = $request->has('aktif') ? 'true' : 'false';

        DB::table('akreditasi')->insert([
            'nama'        => $request->nama,
            'nominal_kip' => $request->nominal_kip,
            'keterangan'  => $request->keterangan,
            'aktif'       => $aktif,
            'urutan'      => $request->urutan,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $akreditasi = Akreditasi::latest()->first();
        AuditLogService::log('buat_akreditasi', 'Akreditasi', $akreditasi->id, null, $request->all());

        return redirect()->route('admin.akreditasi.index')
            ->with('success', 'Akreditasi berhasil ditambahkan.');
    }

    public function edit(Akreditasi $akreditasi)
    {
        return view('admin.akreditasi.edit', compact('akreditasi'));
    }

    public function update(Request $request, Akreditasi $akreditasi)
    {
        $request->validate([
            'nama'        => 'required|string|max:100|unique:akreditasi,nama,' . $akreditasi->id,
            'nominal_kip' => 'required|numeric|min:0',
            'keterangan'  => 'nullable|string',
            'urutan'      => 'required|integer|min:0',
        ]);

        $aktif    = $request->has('aktif') ? 'true' : 'false';
        $dataLama = $akreditasi->toArray();

        DB::table('akreditasi')
            ->where('id', $akreditasi->id)
            ->update([
                'nama'        => $request->nama,
                'nominal_kip' => $request->nominal_kip,
                'keterangan'  => $request->keterangan,
                'aktif'       => $aktif,
                'urutan'      => $request->urutan,
                'updated_at'  => now(),
            ]);

        AuditLogService::log('ubah_akreditasi', 'Akreditasi', $akreditasi->id, $dataLama, $request->all());

        return redirect()->route('admin.akreditasi.index')
            ->with('success', 'Akreditasi berhasil diperbarui.');
    }

    public function destroy(Akreditasi $akreditasi)
    {
        if ($akreditasi->programStudi()->count() > 0) {
            return back()->with(
                'error',
                'Akreditasi tidak dapat dihapus karena masih digunakan oleh ' .
                    $akreditasi->programStudi()->count() . ' program studi.'
            );
        }

        AuditLogService::log('hapus_akreditasi', 'Akreditasi', $akreditasi->id, $akreditasi->toArray(), null);
        $akreditasi->delete();

        return redirect()->route('admin.akreditasi.index')
            ->with('success', 'Akreditasi berhasil dihapus.');
    }
}
