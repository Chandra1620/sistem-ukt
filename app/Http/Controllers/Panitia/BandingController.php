<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\Banding;
use App\Models\GolonganUkt;
use App\Models\RiwayatStatus;
use App\Services\NotifikasiService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BandingController extends Controller
{
    public function index(Request $request)
    {
        $query = Banding::with([
            'pendaftaran.user',
            'pendaftaran.programStudi',
            'hasilUkt',
            'diprosesOleh',
        ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('pendaftaran', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                    ->orWhere('no_pendaftaran', 'like', '%' . $request->search . '%');
            });
        }

        $banding = $query->latest()->paginate(15);

        return view('panitia.banding.index', compact('banding'));
    }

    public function show(Banding $banding)
    {
        $banding->load([
            'pendaftaran.user',
            'pendaftaran.programStudi',
            'pendaftaran.dataEkonomi',
            'pendaftaran.dokumen',
            'pendaftaran.hasilSaw.details.kriteria',
            'hasilUkt',
            'diprosesOleh',
        ]);

        $golongan = GolonganUkt::orderBy('golongan')->get();

        return view('panitia.banding.show', compact('banding', 'golongan'));
    }

    public function proses(Request $request, Banding $banding)
    {
        if (!in_array($banding->status, ['diajukan', 'diproses'])) {
            return back()->with('error', 'Banding ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'keputusan'     => 'required|in:diterima,ditolak',
            'keputusan_panitia' => 'required|string|min:10|max:1000',
            'golongan_baru' => 'required_if:keputusan,diterima|nullable|integer|min:1|max:8',
        ]);

        $pendaftaran  = $banding->pendaftaran;
        $hasilUkt     = $banding->hasilUkt;
        $golonganBaru = null;
        $nominalBaru  = null;
        $statusBandingLama = $banding->status;   // simpan untuk audit yang akurat

        DB::transaction(function () use (
            $request,
            $banding,
            $pendaftaran,
            $hasilUkt,
            &$golonganBaru,
            &$nominalBaru
        ) {
            if ($request->keputusan === 'diterima') {
                $golonganBaru = $request->golongan_baru;
                $nominalBaru  = GolonganUkt::getNominalByGolongan($golonganBaru);

                $hasilUkt->update([
                    'golongan_final'  => $golonganBaru,
                    'nominal_final'   => $nominalBaru,
                    'is_override'     => true,
                    'alasan_override' => 'Banding diterima: ' . $request->keputusan_panitia,
                    'override_oleh'   => Auth::id(),
                    'override_at'     => now(),
                ]);
            }

            $banding->update([
                'status'            => $request->keputusan,
                'keputusan_panitia' => $request->keputusan_panitia,
                'golongan_baru'     => $golonganBaru,
                'nominal_baru'      => $nominalBaru,
                'diproses_oleh'     => Auth::id(),
                'diproses_at'       => now(),
            ]);

            $oldStatus = $pendaftaran->status;
            $pendaftaran->update(['status' => 'selesai']);

            RiwayatStatus::create([
                'pendaftaran_id' => $pendaftaran->id,
                'status_lama'    => $oldStatus,
                'status_baru'    => 'selesai',
                'keterangan'     => 'Banding ' . $request->keputusan . ': ' . $request->keputusan_panitia,
                'diubah_oleh'    => Auth::id(),
            ]);
        });

        NotifikasiService::bandingSelesai(
            $pendaftaran,
            $request->keputusan === 'diterima' ? 'Diterima' : 'Ditolak',
            $golonganBaru
        );

        AuditLogService::log(
            'proses_banding',
            'Banding',
            $banding->id,
            ['status' => $statusBandingLama],           // bukan hardcode 'diajukan'
            ['status' => $request->keputusan, 'golongan_baru' => $golonganBaru]
        );

        return redirect()->route('panitia.banding.index')
            ->with('success', 'Banding berhasil diproses dengan keputusan: ' . ucfirst($request->keputusan));
    }
}
