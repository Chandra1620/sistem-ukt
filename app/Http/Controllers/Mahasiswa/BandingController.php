<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\Banding;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BandingController extends Controller
{
    public function store(Request $request, Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya bisa banding jika status final_ukt
        if ($pendaftaran->status !== 'final_ukt') {
            return back()->with('error', 'Banding hanya dapat diajukan setelah UKT difinalisasi.');
        }

        // Cek apakah sudah pernah banding
        $bandingAktif = Banding::where('pendaftaran_id', $pendaftaran->id)
            ->whereIn('status', ['diajukan', 'diproses'])
            ->exists();

        if ($bandingAktif) {
            return back()->with('error', 'Anda sudah memiliki pengajuan banding yang sedang diproses.');
        }

        $request->validate([
            'alasan' => 'required|string|min:20|max:1000',
        ]);

        $hasilUkt = $pendaftaran->hasilUkt;

        Banding::create([
            'pendaftaran_id' => $pendaftaran->id,
            'hasil_ukt_id'   => $hasilUkt->id,
            'alasan'         => $request->alasan,
            'status'         => 'diajukan',
        ]);

        // Update status pendaftaran
        $oldStatus = $pendaftaran->status;
        $pendaftaran->update(['status' => 'banding_diproses']);

        RiwayatStatus::create([
            'pendaftaran_id' => $pendaftaran->id,
            'status_lama'    => $oldStatus,
            'status_baru'    => 'banding_diproses',
            'keterangan'     => 'Mahasiswa mengajukan banding',
            'diubah_oleh'    => Auth::id(),
        ]);

        return redirect()->route('mahasiswa.hasil-ukt.show', $pendaftaran)
            ->with('success', 'Pengajuan banding berhasil dikirim. Menunggu proses dari panitia.');
    }
}
