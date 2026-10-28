<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pendaftaran;
use App\Models\PeriodeSeleksi;
use App\Models\AuditLog;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Ambil periode aktif atau fallback ke periode terbaru
     */
    private function getPeriodeAktif(): ?PeriodeSeleksi
    {
        return PeriodeSeleksi::where('status', 'aktif')->first()
            ?? PeriodeSeleksi::orderByDesc('id')->first();
    }

    public function index(Request $request)
    {
        $stats = [
            'total_user'      => User::count(),
            'total_mahasiswa' => User::role('mahasiswa')->count(),
            'total_reviewer'  => User::role('reviewer')->count(),
            'total_panitia'   => User::role('panitia')->count(),
        ];

        $daftarPeriode = PeriodeSeleksi::orderByDesc('tahun')->orderByDesc('id')->get();
        $periodeAktif  = $this->getPeriodeAktif();

        // Default: periode aktif. Jika ada input dropdown, hormati pilihannya.
        $selectedParam = $request->query('periode_id') ?? $request->query('periode');
        $selectedPeriodeId = $selectedParam ?? ($periodeAktif ? (string) $periodeAktif->id : 'all');

        $periodeTerpilih = null;
        if ($selectedPeriodeId !== 'all') {
            $periodeTerpilih = $daftarPeriode->firstWhere('id', (int) $selectedPeriodeId) ?? $periodeAktif;
        }

        // Query agregasi tunggal untuk pendaftaran
        $pendaftaranQuery = Pendaftaran::query();
        if ($periodeTerpilih) {
            $pendaftaranQuery->where('periode_id', $periodeTerpilih->id);
        }

        $rawStats = $pendaftaranQuery->selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'menunggu_verifikasi' THEN 1 END) as menunggu_verifikasi,
            COUNT(CASE WHEN status = 'final_ukt' THEN 1 END) as final_ukt,
            COUNT(CASE WHEN status = 'selesai' THEN 1 END) as selesai
        ")->first();

        $pendaftaranStats = [
            'total'               => (int) ($rawStats->total ?? 0),
            'menunggu_verifikasi' => (int) ($rawStats->menunggu_verifikasi ?? 0),
            'final_ukt'           => (int) ($rawStats->final_ukt ?? 0),
            'selesai'             => (int) ($rawStats->selesai ?? 0),
        ];

        $auditTerbaru = AuditLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $roles = Role::all();

        return view('admin.dashboard', compact(
            'stats',
            'periodeAktif',
            'periodeTerpilih',
            'daftarPeriode',
            'selectedPeriodeId',
            'pendaftaranStats',
            'auditTerbaru',
            'roles'
        ));
    }
}
