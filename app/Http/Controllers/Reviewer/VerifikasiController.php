<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\VerifikasiDokumen;
use App\Models\RiwayatStatus;
use App\Services\NotifikasiService;
use App\Jobs\ProsesHitungSaw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::with(['user', 'programStudi', 'periode'])
            ->whereIn('status', [
                'menunggu_verifikasi',
                'diverifikasi',
                'ditolak_reviewer'
            ]);

        if ($request->prodi) {
            $query->where('program_studi_id', $request->prodi);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('no_pendaftaran', 'like', '%' . $request->search . '%');
            });
        }

        $pendaftaran  = $query->latest()->paginate(15);
        $programStudi = \App\Models\ProgramStudi::aktif()->get();

        return view('reviewer.verifikasi.index', compact('pendaftaran', 'programStudi'));
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load([
            'user',
            'programStudi',
            'periode',
            'dataEkonomi',
            'dokumen.jenisDokumen',
            'verifikasi.reviewer',
            'riwayatStatus',
        ]);

        return view('reviewer.verifikasi.show', compact('pendaftaran'));
    }

    public function approve(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'komentar' => 'nullable|string|max:1000',
        ]);

        $konflik = false;

        DB::transaction(function () use ($pendaftaran, $request, &$konflik) {
            // Kunci baris agar reviewer lain menunggu (anti-nabrak / race condition)
            $pendaftaran = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->first();

            // Penjagaan status di dalam transaksi terkunci
            if (!$pendaftaran || $pendaftaran->status !== 'menunggu_verifikasi') {
                $konflik = true;
                return;
            }

            $oldStatus = $pendaftaran->status;

            $pendaftaran->dokumen()->update(['status' => 'valid']);

            VerifikasiDokumen::create([
                'pendaftaran_id' => $pendaftaran->id,
                'reviewer_id'    => Auth::id(),
                'hasil'          => 'approved',
                'komentar'       => $request->komentar,
                'verified_at'    => now(),
            ]);

            $pendaftaran->update(['status' => 'diverifikasi']);

            RiwayatStatus::create([
                'pendaftaran_id' => $pendaftaran->id,
                'status_lama'    => $oldStatus,
                'status_baru'    => 'diverifikasi',
                'keterangan'     => 'Dokumen diverifikasi oleh reviewer',
                'diubah_oleh'    => Auth::id(),
            ]);
        });

        if ($konflik) {
            return back()->with('error', 'Pendaftaran sudah diproses reviewer lain atau tidak dalam status menunggu verifikasi.');
        }

        // Jalankan perhitungan SAW LANGSUNG (tanpa perlu queue worker)
        try {
            ProsesHitungSaw::dispatchSync($pendaftaran);

            return redirect()->route('reviewer.verifikasi.index')
                ->with('success', 'Dokumen diverifikasi dan UKT berhasil dihitung.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                "Gagal hitung SAW pendaftaran {$pendaftaran->id}: " . $e->getMessage()
            );

            // Kembalikan status agar bisa dihitung ulang, jangan nyangkut
            $pendaftaran->update(['status' => 'diverifikasi']);

            return redirect()->route('reviewer.verifikasi.index')
                ->with('error', 'Dokumen diverifikasi, tetapi perhitungan UKT gagal. '
                    . 'Silakan gunakan menu Recalculate SAW.');
        }
    }

    public function reject(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'komentar' => 'required|string|max:1000',
        ]);

        $konflik = false;

        DB::transaction(function () use ($pendaftaran, $request, &$konflik) {
            // Kunci baris agar reviewer lain menunggu (anti-nabrak / race condition)
            $pendaftaran = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->first();

            if (!$pendaftaran || $pendaftaran->status !== 'menunggu_verifikasi') {
                $konflik = true;
                return;
            }

            $oldStatus = $pendaftaran->status;

            $pendaftaran->dokumen()->update(['status' => 'invalid']);

            VerifikasiDokumen::create([
                'pendaftaran_id' => $pendaftaran->id,
                'reviewer_id'    => Auth::id(),
                'hasil'          => 'rejected',
                'komentar'       => $request->komentar,
                'verified_at'    => now(),
            ]);

            $pendaftaran->update(['status' => 'ditolak_reviewer']);

            RiwayatStatus::create([
                'pendaftaran_id' => $pendaftaran->id,
                'status_lama'    => $oldStatus,
                'status_baru'    => 'ditolak_reviewer',
                'keterangan'     => 'Ditolak: ' . $request->komentar,
                'diubah_oleh'    => Auth::id(),
            ]);
        });

        if ($konflik) {
            return back()->with('error', 'Pendaftaran sudah diproses reviewer lain atau tidak dalam status menunggu verifikasi.');
        }

        NotifikasiService::dokumenDitolak($pendaftaran, $request->komentar);

        return redirect()->route('reviewer.verifikasi.index')
            ->with('success', 'Dokumen ditolak. Mahasiswa telah diberi notifikasi.');
    }
}
