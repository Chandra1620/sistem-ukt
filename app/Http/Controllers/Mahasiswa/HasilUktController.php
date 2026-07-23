<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\RiwayatStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HasilUktController extends Controller
{
    public function show(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($pendaftaran->status, [
            'hasil_awal',
            'final_ukt',
            'banding_diproses',
            'selesai'
        ])) {
            return redirect()->route('mahasiswa.pendaftaran.show', $pendaftaran)
                ->with('info', 'Hasil UKT belum tersedia.');
        }

        $pendaftaran->load([
            'programStudi',
            'periode',
            'hasilSaw.details.kriteria',
            'hasilUkt.finalisasiOleh',
            'hasilUkt.overrideOleh',
            'banding.diprosesOleh',
        ]);

        return view('mahasiswa.hasil-ukt.show', compact('pendaftaran'));
    }

    public function terimaKeputusan(Pendaftaran $pendaftaran)
    {
        if ($pendaftaran->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya boleh dari final_ukt (penjagaan status)
        if ($pendaftaran->status !== 'final_ukt') {
            return back()->with('error', 'Keputusan hanya dapat diterima setelah UKT difinalisasi.');
        }

        // Tidak boleh menutup jika masih ada banding berjalan
        $adaBandingAktif = $pendaftaran->banding()
            ->whereIn('status', ['diajukan', 'diproses'])
            ->exists();
        if ($adaBandingAktif) {
            return back()->with('error', 'Masih ada pengajuan banding yang sedang diproses.');
        }

        DB::transaction(function () use ($pendaftaran) {
            $old = $pendaftaran->status;
            $pendaftaran->update(['status' => 'selesai']);

            RiwayatStatus::create([
                'pendaftaran_id' => $pendaftaran->id,
                'status_lama'    => $old,
                'status_baru'    => 'selesai',
                'keterangan'     => 'Mahasiswa menerima keputusan UKT',
                'diubah_oleh'    => Auth::id(),
            ]);
        });

        return back()->with('success', 'Terima kasih, keputusan UKT telah Anda terima. Proses selesai.');
    }
}
