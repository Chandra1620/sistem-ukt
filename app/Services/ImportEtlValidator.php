<?php

namespace App\Services;

use App\Models\Pendaftaran;
use App\Models\ProgramStudi;
use App\Models\Kriteria;
use App\Models\User;
use App\Support\NormalisasiKolom;
use Illuminate\Support\Str;

class ImportEtlValidator
{
    use NormalisasiKolom;

    private array $kriteria;

    // Duplikasi DALAM FILE (dicek saat loop berjalan)
    private array $nikInFile = [];
    private array $hpInFile = [];
    private array $emailInFile = [];
    private array $kipInFile = [];

    // ===== TAMBAHAN: cache referensi, diisi sekali sebelum loop =====
    /** Daftar nama program studi (lowercase) di-load sekali dari DB */
    private array $programStudiNames = [];

    /** Lookup nik/no_hp/email/no_kip yang SUDAH ADA di database (hasil satu whereIn per kolom) */
    private array $existingNiks = [];
    private array $existingHps = [];
    private array $existingEmails = [];
    private array $existingNoKips = [];

    private bool $referensiDimuat = false;

    public function __construct(private int $periodeId)
    {
        $this->kriteria = Kriteria::where('periode_id', $periodeId)
            ->with('subKriteria')
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get()
            ->keyBy('kode')
            ->toArray();

        // Program studi biasanya jumlahnya kecil (puluhan), aman di-load sekali di sini
        $this->programStudiNames = ProgramStudi::pluck('nama')
            ->map(fn($n) => strtolower($n))
            ->all();
    }

    /**
     * Validasi seluruh baris data.
     * Mengembalikan array berisi seluruh error (tidak berhenti di error pertama).
     *
     * @return array{errors: array, summary: array}
     */
    public function validateAll(array $rows): array
    {
        // ===== PRELOAD referensi duplikasi ke database SEKALI untuk semua baris =====
        $this->muatReferensiDuplikasi($rows);

        $errors = [];
        $validCount = 0;

        foreach ($rows as $index => $row) {
            $baris = $index + 2; // baris excel dimulai dari 2 (setelah header)
            $rowErrors = $this->validateRow($row, $baris);

            if (empty($rowErrors)) {
                $validCount++;
            } else {
                $errors = array_merge($errors, $rowErrors);
            }
        }

        $totalBaris = count($rows);
        $barisError = collect($errors)->pluck('baris')->unique()->count();

        return [
            'errors'  => $errors,
            'summary' => [
                'total_baris'  => $totalBaris,
                'total_valid'  => $totalBaris - $barisError,
                'total_tidak_valid' => $barisError,
                'total_error'  => count($errors),
            ],
        ];
    }

    /**
     * Ambil semua nik / no_hp / email / no_kip yang ada di file,
     * lalu cek keberadaannya di database HANYA dengan satu query whereIn per kolom.
     * Ini yang menggantikan ratusan/ribuan query per-baris sebelumnya.
     */
    private function muatReferensiDuplikasi(array $rows): void
    {
        if ($this->referensiDimuat) {
            return;
        }

        $niks = [];
        $hps = [];
        $emails = [];
        $noKips = [];

        foreach ($rows as $row) {
            $nik = trim((string) ($row['nik'] ?? ''));
            if ($nik !== '' && preg_match('/^\d{16}$/', $nik)) {
                $niks[] = $nik;
            }

            $hp = trim((string) ($row['no_hp'] ?? ''));
            if ($hp !== '') {
                $hps[] = $hp;
            }

            $email = trim((string) ($row['email'] ?? ''));
            if ($email !== '') {
                $emails[] = $email;
            }

            $noKip = trim((string) ($row['no_kip'] ?? ''));
            if ($noKip !== '') {
                $noKips[] = $noKip;
            }
        }

        $niks = array_values(array_unique($niks));
        $hps = array_values(array_unique($hps));
        $emails = array_values(array_unique($emails));
        $noKips = array_values(array_unique($noKips));

        // Satu query whereIn per kolom, bukan satu query per baris
        $this->existingNiks = !empty($niks)
            ? Pendaftaran::whereIn('nik', $niks)->pluck('nik')->flip()->all()
            : [];

        $this->existingHps = !empty($hps)
            ? Pendaftaran::whereIn('no_hp', $hps)->pluck('no_hp')->flip()->all()
            : [];

        $this->existingEmails = !empty($emails)
            ? User::whereIn('email', $emails)->pluck('email')->flip()->all()
            : [];

        $this->existingNoKips = !empty($noKips)
            ? Pendaftaran::whereIn('no_kip', $noKips)->pluck('no_kip')->flip()->all()
            : [];

        $this->referensiDimuat = true;
    }

    /**
     * Validasi satu baris, mengembalikan SEMUA error yang ditemukan
     * (tidak berhenti di error pertama).
     */
    private function validateRow(array $row, int $baris): array
    {
        $errors = [];

        // ===== 1. VALIDASI KELENGKAPAN DATA WAJIB =====
        $wajib = [
            'nama_lengkap'  => 'Nama Lengkap',
            'nik'           => 'NIK',
            'no_hp'         => 'Nomor HP',
            'program_studi' => 'Program Studi',
            'asal_daerah'   => 'Asal Daerah',
            'provinsi'      => 'Provinsi',
        ];

        foreach ($wajib as $field => $label) {
            if (empty(trim((string) ($row[$field] ?? '')))) {
                $errors[] = $this->err($baris, $label, $row[$field] ?? '', 'Kolom wajib kosong', "Isi {$label} yang valid");
            }
        }

        // Kriteria wajib (C1-C12)
        foreach ($this->kriteria as $kode => $k) {
            $kolom = $this->kolomKriteria($k['nama']);
            $nilai = $row[$kolom] ?? null;
            if ($nilai === null || trim((string) $nilai) === '') {
                $errors[] = $this->err($baris, $k['nama'], $nilai ?? '', 'Kolom wajib kosong', "Isi {$k['nama']} sesuai pilihan yang tersedia");
            }
        }

        // ===== 2. VALIDASI FORMAT NIK =====
        if (!empty($row['nik'])) {
            $nik = trim((string) $row['nik']);
            if (!preg_match('/^\d{16}$/', $nik)) {
                $errors[] = $this->err($baris, 'NIK', $nik, 'NIK harus 16 digit angka', 'Periksa kembali jumlah digit NIK');
            }
        }

        // ===== 3. VALIDASI FORMAT NO HP =====
        if (!empty($row['no_hp'])) {
            $hp = trim((string) $row['no_hp']);
            if (!preg_match('/^08[0-9]{8,12}$/', $hp)) {
                $errors[] = $this->err($baris, 'Nomor HP', $hp, 'Format nomor HP tidak valid', 'Gunakan format 08xxxxxxxxxx');
            }
        }

        // ===== 4. VALIDASI FORMAT EMAIL (opsional, tapi jika diisi harus valid) =====
        if (!empty($row['email'])) {
            $email = trim((string) $row['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = $this->err($baris, 'Email', $email, 'Format email tidak valid', 'Gunakan format email yang benar, contoh: nama@email.com');
            }
        }

        // ===== 5. VALIDASI PROGRAM STUDI (dari cache in-memory, bukan query per baris) =====
        if (!empty($row['program_studi'])) {
            $prodi = strtolower(trim((string) $row['program_studi']));
            $exists = false;
            foreach ($this->programStudiNames as $namaMaster) {
                if (str_contains($namaMaster, $prodi)) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $errors[] = $this->err($baris, 'Program Studi', $row['program_studi'], 'Data tidak ditemukan di master', 'Periksa nama program studi pada sheet Referensi Program Studi');
            }
        }

        // ===== 6. VALIDASI LOGIKA BISNIS KIP =====
        $isKip = $this->parseBoolean($row['is_kip'] ?? null);
        $noKip = trim((string) ($row['no_kip'] ?? ''));

        if ($isKip === true && $noKip === '') {
            $errors[] = $this->err($baris, 'Nomor KIP', $noKip, 'Nomor KIP wajib diisi jika status KIP = Ya', 'Isi nomor KIP mahasiswa');
        }

        if ($isKip === false && $noKip !== '') {
            $errors[] = $this->err($baris, 'Nomor KIP', $noKip, 'Nomor KIP harus kosong jika status KIP = Tidak', 'Kosongkan kolom Nomor KIP atau ubah status KIP menjadi Ya');
        }

        // ===== 7. VALIDASI KRITERIA (referensi sub kriteria + tipe data) =====
        foreach ($this->kriteria as $kode => $k) {
            $kolom = $this->kolomKriteria($k['nama']);
            $nilai = $row[$kolom] ?? null;

            if ($nilai === null || trim((string) $nilai) === '') {
                continue; // sudah dicatat di validasi kelengkapan
            }

            $found = false;
            foreach ($k['sub_kriteria'] as $sub) {
                if ((float) $sub['nilai'] == (float) $nilai || strtolower(trim($sub['label'])) === strtolower(trim((string) $nilai))) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $pilihan = collect($k['sub_kriteria'])->pluck('label')->implode(', ');
                $errors[] = $this->err($baris, $k['nama'], $nilai, 'Data tidak ditemukan pada pilihan yang tersedia', "Gunakan salah satu: {$pilihan}");
            }
        }

        // ===== 9. VALIDASI DUPLIKASI DALAM FILE =====
        $nik = trim((string) ($row['nik'] ?? ''));
        if ($nik !== '') {
            if (isset($this->nikInFile[$nik])) {
                $errors[] = $this->err($baris, 'NIK', $nik, 'NIK duplikat dalam file (baris ' . $this->nikInFile[$nik] . ')', 'Gunakan NIK yang unik untuk setiap mahasiswa');
            } else {
                $this->nikInFile[$nik] = $baris;
            }
        }

        $hp = trim((string) ($row['no_hp'] ?? ''));
        if ($hp !== '') {
            if (isset($this->hpInFile[$hp])) {
                $errors[] = $this->err($baris, 'Nomor HP', $hp, 'Nomor HP duplikat dalam file (baris ' . $this->hpInFile[$hp] . ')', 'Gunakan nomor HP yang unik');
            } else {
                $this->hpInFile[$hp] = $baris;
            }
        }

        $email = trim((string) ($row['email'] ?? ''));
        if ($email !== '') {
            if (isset($this->emailInFile[$email])) {
                $errors[] = $this->err($baris, 'Email', $email, 'Email duplikat dalam file (baris ' . $this->emailInFile[$email] . ')', 'Gunakan email yang unik');
            } else {
                $this->emailInFile[$email] = $baris;
            }
        }

        if ($noKip !== '') {
            if (isset($this->kipInFile[$noKip])) {
                $errors[] = $this->err($baris, 'Nomor KIP', $noKip, 'Nomor KIP duplikat dalam file (baris ' . $this->kipInFile[$noKip] . ')', 'Periksa kembali nomor KIP');
            } else {
                $this->kipInFile[$noKip] = $baris;
            }
        }

        // ===== 10. VALIDASI DUPLIKASI KE DATABASE (dari cache, bukan query per baris) =====
        if ($nik !== '' && preg_match('/^\d{16}$/', $nik)) {
            if (isset($this->existingNiks[$nik])) {
                $errors[] = $this->err($baris, 'NIK', $nik, 'NIK sudah terdaftar di database', 'Gunakan NIK yang belum terdaftar');
            }
        }

        if ($hp !== '' && isset($this->existingHps[$hp])) {
            $errors[] = $this->err($baris, 'Nomor HP', $hp, 'Nomor HP sudah terdaftar di database', 'Gunakan nomor HP yang belum terdaftar');
        }

        if ($email !== '' && isset($this->existingEmails[$email])) {
            $errors[] = $this->err($baris, 'Email', $email, 'Email sudah terdaftar di database', 'Gunakan email yang belum terdaftar');
        }

        if ($noKip !== '' && isset($this->existingNoKips[$noKip])) {
            $errors[] = $this->err($baris, 'Nomor KIP', $noKip, 'Nomor KIP sudah terdaftar di database', 'Periksa kembali nomor KIP');
        }

        return $errors;
    }

    private function parseBoolean($value): ?bool
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }
        $val = strtolower(trim((string) $value));
        return in_array($val, ['1', 'ya', 'yes', 'true']) ? true : (in_array($val, ['0', 'tidak', 'no', 'false']) ? false : null);
    }

    private function err(int $baris, string $kolom, $nilai, string $kesalahan, string $saran): array
    {
        return [
            'baris'     => $baris,
            'kolom'     => $kolom,
            'nilai'     => (string) $nilai,
            'kesalahan' => $kesalahan,
            'saran'     => $saran,
        ];
    }
}
