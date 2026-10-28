<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Jobs\FinalisasiUktMassalJob;
use App\Models\Pendaftaran;
use App\Models\RiwayatStatus;
use App\Services\NotifikasiService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinalisasiController extends Controller
{
    public function finalisasi(Request $request, Pendaftaran $pendaftaran)
    {
        $hasilUkt = $pendaftaran->hasilUkt;

        if (!$hasilUkt) {
            return back()->with('error', 'Hasil UKT tidak ditemukan.');
        }

        $konflik = false;

        DB::transaction(function () use ($pendaftaran, $hasilUkt, &$konflik) {
            // Kunci baris agar panitia lain menunggu (anti-nabrak / race condition)
            $locked = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->first();

            if (!$locked || $locked->status !== 'hasil_awal') {
                $konflik = true;
                return;
            }

            $hasilUkt->update([
                'finalisasi_oleh' => Auth::id(),
                'finalisasi_at'   => now(),
            ]);

            $oldStatus = $locked->status;
            $locked->update(['status' => 'final_ukt']);

            RiwayatStatus::create([
                'pendaftaran_id' => $locked->id,
                'status_lama'    => $oldStatus,
                'status_baru'    => 'final_ukt',
                'keterangan'     => 'UKT difinalisasi oleh panitia',
                'diubah_oleh'    => Auth::id(),
            ]);
        });

        if ($konflik) {
            return back()->with('error', 'Pendaftaran sudah difinalisasi/diproses panitia lain atau tidak dalam status Hasil Awal.');
        }

        NotifikasiService::uktDifinalisasi(
            $pendaftaran,
            $hasilUkt->golongan_final,
            (float) $hasilUkt->nominal_final
        );

        AuditLogService::log(
            'finalisasi_ukt',
            'HasilUkt',
            $hasilUkt->id,
            null,
            [
                'golongan_final' => $hasilUkt->golongan_final,
                'nominal_final'  => $hasilUkt->nominal_final,
            ]
        );

        return redirect()->route('panitia.hasil-saw.index')
            ->with('success', 'UKT berhasil difinalisasi untuk ' . $pendaftaran->nama_lengkap);
    }

    public function finalisasiMassal(Request $request)
    {
        $periodeId = $request->periode_id ? (int) $request->periode_id : null;

        $totalSiap = Pendaftaran::where('status', 'hasil_awal')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->count();

        if ($totalSiap === 0) {
            return back()->with('error', 'Tidak ada pendaftaran yang siap difinalisasi.');
        }

        // Pindahkan seluruh pemrosesan ke background queue job:
        // - Diproses per chunk kecil (50 pendaftar per batch)
        // - 1 transaksi database independen per chunk
        // - Update massal whereIn()->update()
        // - Bulk insert RiwayatStatus & Notifikasi
        // - Email dikirim melalui DB::afterCommit() di luar transaksi
        FinalisasiUktMassalJob::dispatch($periodeId, Auth::id());

        return redirect()->route('panitia.hasil-saw.index')
            ->with('success', "Finalisasi massal untuk {$totalSiap} mahasiswa sedang diproses di antrean latar belakang.");
    }
}
