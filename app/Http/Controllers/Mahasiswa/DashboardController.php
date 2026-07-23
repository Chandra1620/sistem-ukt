<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $periode     = PeriodeSeleksi::where('status', 'aktif')->first();

        $pendaftaran = Pendaftaran::where('user_id', $user->id)
            ->with(['programStudi', 'hasilUkt', 'periode'])
            ->latest()
            ->first();

        $notifikasiBelumDibaca = DB::table('notifikasi')
            ->where('user_id', $user->id)
            ->whereRaw('"dibaca" IS FALSE')
            ->count();

        $riwayat = Pendaftaran::where('user_id', $user->id)
            ->with(['programStudi', 'hasilUkt'])
            ->latest()
            ->paginate(5);

        return view('mahasiswa.dashboard', compact(
            'user',
            'periode',
            'pendaftaran',
            'notifikasiBelumDibaca',
            'riwayat'
        ));
    }
}
