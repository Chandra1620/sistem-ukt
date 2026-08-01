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
        $periodeId = $request->periode_id;

        $berhasil = 0;

        DB::transaction(function () use ($periodeId, &$berhasil) {
            // Kunci baris yang akan difinalisasi (anti-nabrak saat panitia lain memproses bersamaan)
            $pendaftaranList = Pendaftaran::where('status', 'hasil_awal')
                ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
                ->with('hasilUkt')
                ->lockForUpdate()
                ->get();

            foreach ($pendaftaranList as $pendaftaran) {
                // Lewati bila status sudah berubah (diproses panitia lain)
                if ($pendaftaran->status !== 'hasil_awal') continue;

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

        if ($berhasil === 0) {
            return back()->with('error', 'Tidak ada pendaftaran yang siap difinalisasi.');
        }

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
