<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
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
        if ($pendaftaran->status !== 'hasil_awal') {
            return back()->with('error', 'Pendaftaran tidak dapat difinalisasi pada status ini.');
        }

        $hasilUkt = $pendaftaran->hasilUkt;

        if (!$hasilUkt) {
            return back()->with('error', 'Hasil UKT tidak ditemukan.');
        }

        $hasilUkt->update([
            'finalisasi_oleh' => Auth::id(),
            'finalisasi_at'   => now(),
        ]);

        $oldStatus = $pendaftaran->status;
        $pendaftaran->update(['status' => 'final_ukt']);

        RiwayatStatus::create([
            'pendaftaran_id' => $pendaftaran->id,
            'status_lama'    => $oldStatus,
            'status_baru'    => 'final_ukt',
            'keterangan'     => 'UKT difinalisasi oleh panitia',
            'diubah_oleh'    => Auth::id(),
        ]);

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
        $periodeId = $request->periode_id;

        $pendaftaranList = Pendaftaran::where('status', 'hasil_awal')
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->with('hasilUkt')
            ->get();

        if ($pendaftaranList->isEmpty()) {
            return back()->with('error', 'Tidak ada pendaftaran yang siap difinalisasi.');
        }

        $berhasil = 0;

        DB::transaction(function () use ($pendaftaranList, &$berhasil) {
            foreach ($pendaftaranList as $pendaftaran) {
                $hasilUkt = $pendaftaran->hasilUkt;
                if (!$hasilUkt) continue;

                $hasilUkt->update([
                    'finalisasi_oleh' => Auth::id(),
                    'finalisasi_at'   => now(),
                ]);

                $pendaftaran->update(['status' => 'final_ukt']);

                RiwayatStatus::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'status_lama'    => 'hasil_awal',
                    'status_baru'    => 'final_ukt',
                    'keterangan'     => 'UKT difinalisasi massal oleh panitia',
                    'diubah_oleh'    => Auth::id(),
                ]);

                NotifikasiService::uktDifinalisasi(
                    $pendaftaran,
                    $hasilUkt->golongan_final,
                    (float) $hasilUkt->nominal_final
                );

                $berhasil++;
            }
        });

        AuditLogService::log(
            'finalisasi_massal',
            'Pendaftaran',
            null,
            null,
            ['jumlah' => $berhasil, 'periode_id' => $periodeId]
        );

        return redirect()->route('panitia.hasil-saw.index')
            ->with('success', "Finalisasi massal berhasil untuk {$berhasil} mahasiswa.");
    }
}
