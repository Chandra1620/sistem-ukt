<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramStudiController extends Controller
{
    public function index()
    {
        $programStudi = ProgramStudi::with('akreditasiData')
            ->orderBy('jenjang')
            ->orderBy('nama')
            ->get();

        return view('admin.program-studi.index', compact('programStudi'));
    }

    public function create()
    {
        return view('admin.program-studi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'jenjang' => 'required|in:D3,D4',
            'akreditasi_id' => 'required|exists:akreditasi,id',
        ]);

        $aktif = $request->has('aktif') ? 'true' : 'false';

        DB::table('program_studi')->insert([
            'nama'          => $request->nama,
            'jenjang'       => $request->jenjang,
            'akreditasi_id' => $request->akreditasi_id,
            'aktif'         => $aktif,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $prodi = ProgramStudi::latest()->first();

        AuditLogService::log('buat_program_studi', 'ProgramStudi', $prodi->id, null, $request->all());

        return redirect()->route('admin.program-studi.index')
            ->with('success', 'Program studi berhasil ditambahkan.');
    }

    public function edit(ProgramStudi $programStudi)
    {
        return view('admin.program-studi.edit', compact('programStudi'));
    }

    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'jenjang' => 'required|in:D3,D4',
            'akreditasi_id' => 'required|exists:akreditasi,id',
        ]);

        $aktif = $request->has('aktif') ? 'true' : 'false';
        $dataLama = $programStudi->toArray();

        DB::table('program_studi')
            ->where('id', $programStudi->id)
            ->update([
                'nama'          => $request->nama,
                'jenjang'       => $request->jenjang,
                'akreditasi_id' => $request->akreditasi_id,
                'aktif'         => $aktif,
                'updated_at'    => now(),
            ]);

        AuditLogService::log('ubah_program_studi', 'ProgramStudi', $programStudi->id, $dataLama, $request->all());

        return redirect()->route('admin.program-studi.index')
            ->with('success', 'Program studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        if ($programStudi->pendaftaran()->count() > 0) {
            return back()->with('error', 'Program studi tidak dapat dihapus karena sudah ada pendaftar.');
        }

        AuditLogService::log('hapus_program_studi', 'ProgramStudi', $programStudi->id, $programStudi->toArray(), null);
        $programStudi->delete();

        return redirect()->route('admin.program-studi.index')
            ->with('success', 'Program studi berhasil dihapus.');
    }
}
