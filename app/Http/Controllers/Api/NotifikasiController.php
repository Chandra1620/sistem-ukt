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
use Illuminate\Support\Facades\Log;

class NotifikasiController extends Controller
{
    public function index()
    {
        // Auto-generate notifikasi penting (SADAR ROLE) sebelum tampilkan.
        // Dibungkus try/catch agar hiccup DB saat generate tidak
        // menggagalkan tampilan notifikasi yang sudah ada.
        try {
            $this->generateNotifikasiOtomatis();
        } catch (\Throwable $e) {
            Log::warning('[NOTIF] generate otomatis gagal: ' . $e->getMessage());
        }

        $notifikasi = Notifikasi::where('user_id', Auth::id())
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
            ]);

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
        // Tetap generate agar badge muncul walau dropdown belum dibuka
        // Polling ringan (tiap 10 dtk): HANYA hitung, JANGAN generate,
        // supaya hemat round-trip ke DB remote. Generate ada di index().
        return response()->json([
            'jumlah_belum_dibaca' => $this->hitungBelumDibaca(),
        ]);
    }

    public function baca($id)
    {
        Notifikasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'dibaca'    => 'true', // string, bukan boolean PHP (Postgres binding issue)
                'dibaca_at' => now(),
            ]);

        return response()->json([
            'success'             => true,
            'jumlah_belum_dibaca' => $this->hitungBelumDibaca(),
        ]);
    }

    public function bacaSemua()
    {
        Notifikasi::where('user_id', Auth::id())
            ->whereRaw('"dibaca" = FALSE') // aman untuk boolean Postgres (JANGAN where('dibaca', false))
            ->update([
                'dibaca'    => 'true', // string, bukan boolean PHP (Postgres binding issue)
                'dibaca_at' => now(),
            ]);

        return response()->json([
            'success'             => true,
            'jumlah_belum_dibaca' => 0,
        ]);
    }

    private function hitungBelumDibaca(): int
    {
        return Notifikasi::where('user_id', Auth::id())
            ->whereRaw('"dibaca" = FALSE')
            ->count();
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

        // 1. Pendaftaran menunggu verifikasi -> admin & reviewer
        if ($isAdmin || $isReviewer) {
            $menunggu = Pendaftaran::where('status', 'menunggu_verifikasi')->count();
            if ($menunggu > 0) {
                $this->upsertNotif(
                    $userId,
                    'pendaftaran_baru',
                    "{$menunggu} Pendaftaran Menunggu Verifikasi",
                    "{$menunggu} mahasiswa telah mengajukan pendaftaran dan menunggu verifikasi dokumen.",
                );
            }
        }

        // 2. Banding baru belum diproses -> panitia & admin
        if ($isPanitia || $isAdmin) {
            $banding = Banding::where('status', 'diajukan')->count();
            if ($banding > 0) {
                $this->upsertNotif(
                    $userId,
                    'banding_baru',
                    "{$banding} Pengajuan Banding Baru",
                    "{$banding} mahasiswa mengajukan banding yang perlu diproses oleh panitia.",
                );
            }
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
        if ($isAdmin) {
            $importGagal = ImportLog::where('status', 'gagal')
                ->where('created_at', '>=', now()->subDays(3))
                ->count();
            if ($importGagal > 0) {
                $this->upsertNotif(
                    $userId,
                    'import_gagal',
                    "{$importGagal} Proses Import Gagal",
                    "{$importGagal} proses import ETL gagal dalam 3 hari terakhir. Periksa log import untuk detail.",
                );
            }
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
