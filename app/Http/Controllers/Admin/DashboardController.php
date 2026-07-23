<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\AuditLog;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_user'      => User::count(),
            'total_mahasiswa' => User::role('mahasiswa')->count(),
            'total_reviewer'  => User::role('reviewer')->count(),
            'total_panitia'   => User::role('panitia')->count(),
        ];

        $periodeAktif = PeriodeSeleksi::where('status', 'aktif')->first();

        $pendaftaranStats = [
            'total'               => Pendaftaran::count(),
            'menunggu_verifikasi' => Pendaftaran::where('status', 'menunggu_verifikasi')->count(),
            'final_ukt'           => Pendaftaran::where('status', 'final_ukt')->count(),
            'selesai'             => Pendaftaran::where('status', 'selesai')->count(),
        ];

        $auditTerbaru = AuditLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $roles = Role::all();

        return view('admin.dashboard', compact(
            'stats',
            'periodeAktif',
            'pendaftaranStats',
            'auditTerbaru',
            'roles'
        ));
    }
}
