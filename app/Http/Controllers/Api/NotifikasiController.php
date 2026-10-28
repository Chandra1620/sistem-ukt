<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\Banding;
use App\Models\ImportLog;
use App\Models\PeriodeSeleksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotifikasiController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([]);
        }

        // Auto-generate notifikasi penting (SADAR ROLE) sebelum tampilkan.
        // Dibatasi per user via cache 60 detik agar tidak membebani database setiap request.
        try {
            Cache::remember("user_{$userId}_auto_notif_generated", 60, function () {
                $this->generateNotifikasiOtomatis();
                return true;
            });
        } catch (\Throwable $e) {
            Log::warning('[NOTIF] generate otomatis gagal: ' . $e->getMessage());
        }

        $cacheKey = "user_{$userId}_notifikasi_list";
        $notifikasi = Cache::remember($cacheKey, 60, function () use ($userId) {
            return Notifikasi::where('user_id', $userId)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
                ->map(fn($n) => [
                    'id'         => $n->id,
                    'judul'      => $n->judul,
                    'pesan'      => $n->pesan,
                    'tipe'       => $n->tipe,
                    'ikon'       => $this->getIkon($n->tipe),
                    'dibaca'     => (bool) $n->dibaca,
                    'created_at' => optional($n->created_at)->toISOString(),
                ])
                ->all();
        });

        // Front-end mengharapkan ARRAY langsung (data.filter(...)),
        // BUKAN object. Kembalikan koleksi apa adanya.
        return response()->json($notifikasi);
    }

    /**
     * Jumlah notifikasi belum dibaca (untuk badge). Dipanggil polling ringan
     * front-end: GET /api/notifikasi/jumlah
     */
    public function jumlah()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['jumlah_belum_dibaca' => 0]);
        }

        $cacheKey = "user_{$userId}_notif_unread_count";
        $jumlahBelumDibaca = Cache::remember($cacheKey, 60, function () use ($userId) {
            return $this->hitungBelumDibaca($userId);
        });

        return response()->json([
            'jumlah_belum_dibaca' => (int) $jumlahBelumDibaca,
        ]);
    }

    public function baca($id)
    {
        $userId = Auth::id();

        Notifikasi::where('id', $id)
            ->where('user_id', $userId)
            ->update([
                'dibaca'    => 'true', // string, bukan boolean PHP (Postgres binding issue)
                'dibaca_at' => now(),
            ]);

        Cache::forget("user_{$userId}_notif_unread_count");
        Cache::forget("user_{$userId}_notifikasi_list");

        return response()->json([
            'success'             => true,
            'jumlah_belum_dibaca' => $this->hitungBelumDibaca($userId),
        ]);
    }

    public function bacaSemua()
    {
        $userId = Auth::id();

        Notifikasi::where('user_id', $userId)
            ->whereRaw('"dibaca" = FALSE') // aman untuk boolean Postgres (JANGAN where('dibaca', false))
            ->update([
                'dibaca'    => 'true', // string, bukan boolean PHP (Postgres binding issue)
                'dibaca_at' => now(),
            ]);

        Cache::forget("user_{$userId}_notif_unread_count");
        Cache::forget("user_{$userId}_notifikasi_list");

        return response()->json([
            'success'             => true,
            'jumlah_belum_dibaca' => 0,
        ]);
    }

    private function hitungBelumDibaca(?int $userId = null): int
    {
        $userId = $userId ?? Auth::id();
        if (!$userId) {
            return 0;
        }

        return (int) Notifikasi::where('user_id', $userId)
            ->selectRaw('COALESCE(SUM(CASE WHEN dibaca = FALSE THEN 1 ELSE 0 END), 0) as unread')
            ->value('unread');
    }

    private function getIkon(string $tipe): string
    {
        return match ($tipe) {
            'pendaftaran_baru'    => 'bi bi-person-plus-fill',
            'siap_verifikasi'     => 'bi bi-clipboard-check-fill',
            'banding_baru'        => 'bi bi-chat-square-text-fill',
            'periode_berakhir'    => 'bi bi-calendar-x-fill',
            'periode_nonaktif'    => 'bi bi-calendar-event-fill',
            'import_gagal'        => 'bi bi-exclamation-triangle-fill',
            'import_selesai'      => 'bi bi-cloud-check-fill',
            'dokumen_ditolak'     => 'bi bi-x-circle-fill',
            'hasil_ukt'           => 'bi bi-check-circle-fill',
            'banding_selesai'     => 'bi bi-info-circle-fill',
            'saw_gagal'           => 'bi bi-exclamation-octagon-fill',
            default               => 'bi bi-bell-fill',
        };
    }

    /**
     * Generate notifikasi otomatis SADAR ROLE (idempoten via upsertNotif).
     *
     * - Mahasiswa: TIDAK ada auto-notif di sini; notifikasinya berbasis event
     *   (dokumen ditolak, hasil UKT, banding selesai) via NotifikasiService.
     * - Admin: pendaftaran menunggu, banding, status periode, import gagal.
     * - Reviewer: pendaftaran siap diverifikasi.
     * - Panitia: banding baru.
     * - Management: status periode (pengawasan).
     */
    private function generateNotifikasiOtomatis(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return;
        }
        $userId = $user->id;

        $isAdmin      = $user->hasRole('admin');
        $isReviewer   = $user->hasRole('reviewer');
        $isPanitia    = $user->hasRole('panitia');
        $isManagement = $user->hasRole('management');

        // Gabungkan hitungan count menjadi satu query bila user admin/panitia/reviewer
        $menunggu    = 0;
        $banding     = 0;
        $importGagal = 0;

        if ($isAdmin) {
            $counts = DB::selectOne("
                SELECT 
                    (SELECT COUNT(*) FROM pendaftaran WHERE status = 'menunggu_verifikasi') AS menunggu,
                    (SELECT COUNT(*) FROM banding WHERE status = 'diajukan') AS banding,
                    (SELECT COUNT(*) FROM import_logs WHERE status = 'gagal' AND created_at >= NOW() - INTERVAL '3 days') AS import_gagal
            ");
            $menunggu    = (int) ($counts->menunggu ?? 0);
            $banding     = (int) ($counts->banding ?? 0);
            $importGagal = (int) ($counts->import_gagal ?? 0);
        } else {
            if ($isReviewer) {
                $menunggu = Pendaftaran::where('status', 'menunggu_verifikasi')->count();
            }
            if ($isPanitia) {
                $banding = Banding::where('status', 'diajukan')->count();
            }
        }

        // 1. Pendaftaran menunggu verifikasi -> admin & reviewer
        if (($isAdmin || $isReviewer) && $menunggu > 0) {
            $this->upsertNotif(
                $userId,
                'pendaftaran_baru',
                "{$menunggu} Pendaftaran Menunggu Verifikasi",
                "{$menunggu} mahasiswa telah mengajukan pendaftaran dan menunggu verifikasi dokumen.",
            );
        }

        // 2. Banding baru belum diproses -> panitia & admin
        if (($isPanitia || $isAdmin) && $banding > 0) {
            $this->upsertNotif(
                $userId,
                'banding_baru',
                "{$banding} Pengajuan Banding Baru",
                "{$banding} mahasiswa mengajukan banding yang perlu diproses oleh panitia.",
            );
        }

        // 3 & 4. Status periode -> admin & management (pengawasan)
        if ($isAdmin || $isManagement) {
            $periodeHampirSelesai = PeriodeSeleksi::where('status', 'aktif')
                ->whereDate('tanggal_selesai', '<=', now()->addDays(7))
                ->whereDate('tanggal_selesai', '>=', now())
                ->first();

            if ($periodeHampirSelesai) {
                $sisaHari = now()->diffInDays($periodeHampirSelesai->tanggal_selesai);
                $this->upsertNotif(
                    $userId,
                    'periode_berakhir',
                    "Periode Aktif Akan Berakhir",
                    "Periode \"{$periodeHampirSelesai->nama}\" akan berakhir dalam {$sisaHari} hari ({$periodeHampirSelesai->tanggal_selesai->format('d/m/Y')}).",
                );
            }

            $adaPeriodeAktif = PeriodeSeleksi::where('status', 'aktif')->exists();
            if (!$adaPeriodeAktif) {
                $this->upsertNotif(
                    $userId,
                    'periode_nonaktif',
                    "Tidak Ada Periode Aktif",
                    "Saat ini tidak ada periode seleksi yang aktif. Aktifkan periode agar mahasiswa dapat mendaftar.",
                );
            }
        }

        // 5. Import ETL gagal -> admin saja
        if ($isAdmin && $importGagal > 0) {
            $this->upsertNotif(
                $userId,
                'import_gagal',
                "{$importGagal} Proses Import Gagal",
                "{$importGagal} proses import ETL gagal dalam 3 hari terakhir. Periksa log import untuk detail.",
            );
        }
    }

    /**
     * Insert atau update notifikasi berdasarkan tipe (satu per tipe, update bila belum dibaca)
     */
    private function upsertNotif(int $userId, string $tipe, string $judul, string $pesan): void
    {
        $notif = Notifikasi::where('user_id', $userId)
            ->where('tipe', $tipe)
            ->whereRaw('"dibaca" = FALSE')
            ->first();

        if ($notif) {
            $notif->update([
                'judul'      => $judul,
                'pesan'      => $pesan,
                'updated_at' => now(),
            ]);
            return;
        }

        Notifikasi::create([
            'user_id'    => $userId,
            'tipe'       => $tipe,
            'dibaca'     => 'false', // string, bukan boolean PHP (Postgres binding issue)
            'judul'      => $judul,
            'pesan'      => $pesan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
