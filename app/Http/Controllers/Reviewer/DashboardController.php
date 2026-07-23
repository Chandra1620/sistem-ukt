<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\VerifikasiDokumen;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'menunggu_verifikasi' => Pendaftaran::where('status', 'menunggu_verifikasi')->count(),
            'diverifikasi'        => Pendaftaran::where('status', 'diverifikasi')->count(),
            'ditolak'             => Pendaftaran::where('status', 'ditolak_reviewer')->count(),
            'total_diverifikasi'  => VerifikasiDokumen::where('reviewer_id', $user->id)->count(),
        ];

        $antrian = Pendaftaran::where('status', 'menunggu_verifikasi')
            ->with(['user', 'programStudi', 'periode'])
            ->latest()
            ->limit(10)
            ->get();

        $riwayatVerifikasi = VerifikasiDokumen::where('reviewer_id', $user->id)
            ->with(['pendaftaran.user', 'pendaftaran.programStudi'])
            ->latest()
            ->limit(5)
            ->get();

        return view('reviewer.dashboard', compact(
            'stats',
            'antrian',
            'riwayatVerifikasi'
        ));
    }
}
