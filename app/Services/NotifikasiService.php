<?php

namespace App\Services;

use App\Mail\DokumenDitolakMail;
use App\Mail\HasilUktMail;
use App\Mail\BandingSelesaiMail;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotifikasiService
{
    public static function kirim(
        int $userId,
        string $judul,
        string $pesan,
        string $tipe,
        array $data = []
    ): void {
        Notifikasi::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'data'    => $data,
        ]);
    }

    public static function dokumenDitolak(Pendaftaran $pendaftaran, string $alasan): void
    {
        self::kirim(
            $pendaftaran->user_id,
            'Dokumen Ditolak',
            "Dokumen Anda ditolak. Alasan: {$alasan}. Silakan upload ulang.",
            'dokumen_ditolak',
            ['pendaftaran_id' => $pendaftaran->id, 'alasan' => $alasan]
        );

        // Kirim email
        try {
            Mail::to($pendaftaran->user->email)
                ->queue(new DokumenDitolakMail($pendaftaran, $alasan));
        } catch (\Exception $e) {
            Log::error('Email gagal dikirim: ' . $e->getMessage());
        }
    }

    public static function hasilUktKeluar(
        Pendaftaran $pendaftaran,
        int $golongan,
        float $nominal
    ): void {
        $nominalFormat = 'Rp ' . number_format($nominal, 0, ',', '.');
        self::kirim(
            $pendaftaran->user_id,
            'Hasil UKT Telah Keluar',
            "Hasil UKT: Golongan {$golongan} ({$nominalFormat})",
            'hasil_ukt',
            ['pendaftaran_id' => $pendaftaran->id, 'golongan' => $golongan]
        );

        // Kirim email
        try {
            Mail::to($pendaftaran->user->email)
                ->queue(new HasilUktMail($pendaftaran, $golongan, $nominal));
        } catch (\Exception $e) {
            Log::error('Email gagal dikirim: ' . $e->getMessage());
        }
    }

    public static function uktDifinalisasi(
        Pendaftaran $pendaftaran,
        int $golongan,
        float $nominal
    ): void {
        $nominalFormat = 'Rp ' . number_format($nominal, 0, ',', '.');
        self::kirim(
            $pendaftaran->user_id,
            'UKT Telah Difinalisasi',
            "UKT Anda telah difinalisasi: Golongan {$golongan} ({$nominalFormat})",
            'hasil_ukt',
            ['pendaftaran_id' => $pendaftaran->id, 'golongan' => $golongan]
        );

        // Kirim email
        try {
            Mail::to($pendaftaran->user->email)
                ->queue(new HasilUktMail($pendaftaran, $golongan, $nominal));
        } catch (\Exception $e) {
            Log::error('Email gagal dikirim: ' . $e->getMessage());
        }
    }

    public static function bandingSelesai(
        Pendaftaran $pendaftaran,
        string $keputusan,
        ?int $golonganBaru = null
    ): void {
        $pesan = "Banding Anda telah diproses: {$keputusan}.";
        if ($golonganBaru) {
            $pesan .= " Golongan baru: {$golonganBaru}.";
        }

        self::kirim(
            $pendaftaran->user_id,
            'Banding Telah Diproses',
            $pesan,
            'banding_selesai',
            ['pendaftaran_id' => $pendaftaran->id, 'keputusan' => $keputusan]
        );

        try {
            Mail::to($pendaftaran->user->email)
                ->queue(new BandingSelesaiMail($pendaftaran, $keputusan, $golonganBaru));
        } catch (\Exception $e) {
            Log::error('Email gagal dikirim: ' . $e->getMessage());
        }
    }

    public static function sawGagal(Pendaftaran $pendaftaran): void
    {
        $pesan = "Perhitungan SAW gagal untuk pendaftaran "
            . "#{$pendaftaran->no_pendaftaran} ({$pendaftaran->nama_lengkap}). "
            . "Silakan jalankan Recalculate SAW.";

        // Kirim ke semua admin (bukan ke mahasiswa, karena ini kegagalan teknis)
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            self::kirim(
                $admin->id,
                'Perhitungan SAW Gagal',
                $pesan,
                'saw_gagal',
                ['pendaftaran_id' => $pendaftaran->id]
            );
        }
    }
}
