<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisDokumen;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisDokumenController extends Controller
{
    public function index()
    {
        $jenis = JenisDokumen::orderBy('urutan')->get();
        return view('admin.jenis-dokumen.index', compact('jenis'));
    }

    public function create()
    {
        return view('admin.jenis-dokumen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'urutan'     => 'required|integer|min:0',
        ]);

        $wajib = $request->has('wajib') ? true : false;
        $aktif = $request->has('aktif') ? true : false;

        DB::table('jenis_dokumen')->insert([
            'nama'        => $request->nama,
            'keterangan'  => $request->keterangan,
            'wajib'       => $wajib ? 'true' : 'false',
            'aktif'       => $aktif ? 'true' : 'false',
            'urutan'      => (int) $request->urutan,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $jenis = JenisDokumen::latest()->first();

        AuditLogService::log('buat_jenis_dokumen', 'JenisDokumen', $jenis->id, null, $request->all());

        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function edit(JenisDokumen $jenisDokumen)
    {
        return view('admin.jenis-dokumen.edit', compact('jenisDokumen'));
    }

    public function update(Request $request, JenisDokumen $jenisDokumen)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'urutan'     => 'required|integer|min:0',
        ]);

        $wajib = $request->has('wajib') ? true : false;
        $aktif = $request->has('aktif') ? true : false;

        $dataLama = $jenisDokumen->toArray();

        DB::table('jenis_dokumen')
            ->where('id', $jenisDokumen->id)
            ->update([
                'nama'       => $request->nama,
                'keterangan' => $request->keterangan,
                'wajib'      => $wajib ? 'true' : 'false',
                'aktif'      => $aktif ? 'true' : 'false',
                'urutan'     => (int) $request->urutan,
                'updated_at' => now(),
            ]);

        AuditLogService::log('ubah_jenis_dokumen', 'JenisDokumen', $jenisDokumen->id, $dataLama, $request->all());

        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    public function destroy(JenisDokumen $jenisDokumen)
    {
        if ($jenisDokumen->dokumen()->count() > 0) {
            return back()->with('error', 'Jenis dokumen tidak dapat dihapus karena sudah digunakan.');
        }

        AuditLogService::log('hapus_jenis_dokumen', 'JenisDokumen', $jenisDokumen->id, $jenisDokumen->toArray(), null);
        $jenisDokumen->delete();

        return redirect()->route('admin.jenis-dokumen.index')
            ->with('success', 'Jenis dokumen berhasil dihapus.');
    }
}
