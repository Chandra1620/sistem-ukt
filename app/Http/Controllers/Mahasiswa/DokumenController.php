<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    public function store(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) abort(403);

        if (!$pendaftaran->bisaDiedit()) {
            return back()->with('error', 'Dokumen tidak dapat diupload pada status ini.');
        }

        // Ambil jenis dokumen yang valid dari database
        $jenisValid = JenisDokumen::whereRaw('"aktif" IS TRUE')
            ->pluck('id')
            ->toArray();

        $request->validate([
            'jenis_dokumen_id' => 'required|in:' . implode(',', $jenisValid),
            'file'             => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $jenisDokumen = JenisDokumen::findOrFail($request->jenis_dokumen_id);

        // Hapus dokumen lama jika ada
        $existing = $pendaftaran->dokumen()
            ->where('jenis_dokumen_id', $request->jenis_dokumen_id)
            ->first();
        if ($existing) {
            Storage::disk('local')->delete($existing->path);
            $existing->delete();
        }

        $file     = $request->file('file');
        $namaFile = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs(
            'dokumen/' . $pendaftaran->id,
            $namaFile,
            'local'
        );

        Dokumen::create([
            'pendaftaran_id'   => $pendaftaran->id,
            'jenis_dokumen_id' => $jenisDokumen->id,
            'jenis'            => $jenisDokumen->nama, // legacy field
            'nama_file'        => $file->getClientOriginalName(),
            'path'             => $path,
            'mime_type'        => $file->getMimeType(),
            'ukuran'           => $file->getSize(),
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function destroy(Dokumen $dokumen)
    {
        $pendaftaran = $dokumen->pendaftaran;

        if ($pendaftaran->user_id !== Auth::id()) abort(403);

        if (!$pendaftaran->bisaDiedit()) {
            return back()->with('error', 'Dokumen tidak dapat dihapus pada status ini.');
        }

        Storage::disk('local')->delete($dokumen->path);
        $dokumen->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function preview(Dokumen $dokumen)
    {
        $pendaftaran = $dokumen->pendaftaran;
        $user        = Auth::user();

        if ($user->hasRole('mahasiswa') && $pendaftaran->user_id !== $user->id) {
            abort(403);
        }

        if (!Storage::disk('local')->exists($dokumen->path)) {
            abort(404, 'File tidak ditemukan.');
        }
        $path = Storage::disk('local')->path($dokumen->path);
        $mimeType = $dokumen->mime_type;

        return response()->file($path, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $dokumen->nama_file . '"',
        ]);
    }
}
