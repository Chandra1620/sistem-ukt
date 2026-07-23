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

class NotifikasiController extends Controller
{
    public function index()
    {
        // Auto-generate notifikasi penting sebelum tampilkan
        $this->generateNotifikasiOtomatis();

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
                'created_at' => $n->created_at->toISOString(),
            ]);

        return response()->json($notifikasi);
    }

    public function baca($id)
    {
        Notifikasi::where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['dibaca' => 'true']); // string, bukan boolean PHP (Postgres binding issue)

        return response()->json(['success' => true]);
    }

    public function bacaSemua()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('dibaca', false)
            ->update(['dibaca' => 'true']); // string, bukan boolean PHP (Postgres binding issue)

        return response()->json(['success' => true]);
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
            default               => 'bi bi-bell-fill',
        };
    }

    /**
     * Generate notifikasi penting secara otomatis (idempoten - tidak duplikat)
     */
    private function generateNotifikasiOtomatis(): void
    {
        $userId = Auth::id();

        // 1. Pendaftaran baru menunggu verifikasi
        $menunggu = Pendaftaran::where('status', 'menunggu_verifikasi')->count();
        if ($menunggu > 0) {
            $this->upsertNotif(
                $userId,
                'pendaftaran_baru',
                "{$menunggu} Pendaftaran Menunggu Verifikasi",
                "{$menunggu} mahasiswa telah mengajukan pendaftaran dan menunggu verifikasi dokumen.",
            );
        }

        // 2. Banding baru yang belum diproses
        $banding = Banding::where('status', 'diajukan')->count();
        if ($banding > 0) {
            $this->upsertNotif(
                $userId,
                'banding_baru',
                "{$banding} Pengajuan Banding Baru",
                "{$banding} mahasiswa mengajukan banding yang perlu diproses oleh panitia.",
            );
        }

        // 3. Periode yang akan berakhir dalam 7 hari
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

        // 4. Tidak ada periode aktif
        $adaPeriodeAktif = PeriodeSeleksi::where('status', 'aktif')->exists();
        if (!$adaPeriodeAktif) {
            $this->upsertNotif(
                $userId,
                'periode_nonaktif',
                "Tidak Ada Periode Aktif",
                "Saat ini tidak ada periode seleksi yang aktif. Aktifkan periode agar mahasiswa dapat mendaftar.",
            );
        }

        // 5. Import ETL yang gagal (belum dilihat)
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

    /**
     * Insert atau update notifikasi berdasarkan tipe (satu notifikasi per tipe, update jika sudah ada dan belum dibaca)
     */
    private function upsertNotif(int $userId, string $tipe, string $judul, string $pesan): void
    {
        $notif = Notifikasi::where('user_id', $userId)
            ->where('tipe', $tipe)
            ->whereRaw('"dibaca" = FALSE')   // aman untuk kolom boolean Postgres
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
            'dibaca'     => 'false',   // string, bukan boolean PHP (Laravel mengubah boolean jadi integer
            // saat prepareBindings, dan Postgres menolak integer untuk kolom boolean)
            'judul'      => $judul,
            'pesan'      => $pesan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
