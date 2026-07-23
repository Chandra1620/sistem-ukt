<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\GolonganUkt;
use App\Models\RiwayatStatus;
use App\Services\NotifikasiService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OverrideController extends Controller
{
    public function override(Request $request, Pendaftaran $pendaftaran)
    {
        if (!in_array($pendaftaran->status, ['hasil_awal', 'final_ukt'])) {
            return back()->with('error', 'Override hanya dapat dilakukan pada status Hasil Awal atau Final UKT.');
        }

        $request->validate([
            'golongan_baru' => 'required|integer|min:1|max:8',
            'alasan'        => 'required|string|min:20|max:1000',
        ]);

        $hasilUkt = $pendaftaran->hasilUkt;

        if (!$hasilUkt) {
            return back()->with('error', 'Hasil UKT tidak ditemukan.');
        }

        $golonganLama  = $hasilUkt->golongan_final;
        $nominalLama   = $hasilUkt->nominal_final;
        $nominalBaru   = GolonganUkt::getNominalByGolongan($request->golongan_baru);

        // Simpan data lama untuk audit
        $dataLama = [
            'golongan_final' => $golonganLama,
            'nominal_final'  => $nominalLama,
            'is_override'    => $hasilUkt->is_override,
        ];

        // Update hasil UKT
        $hasilUkt->update([
            'golongan_final'  => $request->golongan_baru,
            'nominal_final'   => $nominalBaru,
            'is_override'     => DB::raw('TRUE'),
            'alasan_override' => $request->alasan,
            'override_oleh'   => Auth::id(),
            'override_at'     => now(),
        ]);

        // Simpan status lama SEBELUM diubah
        $statusLama = $pendaftaran->status;

        if ($statusLama === 'hasil_awal') {
            $pendaftaran->update(['status' => 'final_ukt']);
        }

        RiwayatStatus::create([
            'pendaftaran_id' => $pendaftaran->id,
            'status_lama'    => $statusLama, // benar
            'status_baru'    => 'final_ukt',
            'keterangan'     => 'Override UKT: Golongan ' . $golonganLama . ' → ' . $request->golongan_baru,
            'diubah_oleh'    => Auth::id(),
        ]);

        // Kirim notifikasi
        NotifikasiService::uktDifinalisasi(
            $pendaftaran,
            $request->golongan_baru,
            $nominalBaru
        );

        // Audit log
        AuditLogService::log(
            'override_ukt',
            'HasilUkt',
            $hasilUkt->id,
            $dataLama,
            [
                'golongan_final'  => $request->golongan_baru,
                'nominal_final'   => $nominalBaru,
                'alasan_override' => $request->alasan,
            ]
        );

        return redirect()->route('panitia.hasil-saw.index')
            ->with('success', 'Override UKT berhasil. Golongan diubah dari ' . $golonganLama . ' ke ' . $request->golongan_baru);
    }
}
