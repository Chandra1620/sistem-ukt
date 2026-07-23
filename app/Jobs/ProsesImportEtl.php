<?php

namespace App\Jobs;

use App\Models\ImportLog;
use App\Models\Pendaftaran;
use App\Models\DataEkonomi;
use App\Models\PeriodeSeleksi;
use App\Models\ProgramStudi;
use App\Models\Kriteria;
use App\Services\SawService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Support\NormalisasiKolom;
use App\Support\PemetaanDataEkonomi;

class ProsesImportEtl implements ShouldQueue
{
    use NormalisasiKolom;
    use PemetaanDataEkonomi;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300;

    public function __construct(
        public array      $rows,
        public int        $periodeId,
        public ImportLog  $importLog
    ) {}

    public function handle(SawService $sawService): void
    {
        $log       = $this->importLog;
        $periodeId = $this->periodeId;

        $log->update(['status' => 'processing']);

        $__mulaiProses = microtime(true); // [TEST CASE] mulai ukur pemrosesan

        $berhasil = 0;
        $gagal    = 0;
        $duplikat = 0;
        $errors   = [];

        // Ambil kriteria periode (untuk mapping sub kriteria)
        $kriteria = Kriteria::where('periode_id', $periodeId)
            ->with('subKriteria')
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get()
            ->keyBy('kode');

        foreach ($this->rows as $index => $row) {
            $baris = $index + 2;

            try {
                // Validasi dasar (safety net - validasi utama sudah dilakukan
                // di ImportEtlValidator sebelum job ini di-dispatch)
                if (empty($row['nik']) || !preg_match('/^\d{16}$/', (string) $row['nik'])) {
                    $errors[] = "Baris {$baris}: NIK tidak valid ({$row['nik']})";
                    $gagal++;
                    continue;
                }

                if (Pendaftaran::where('nik', $row['nik'])->exists()) {
                    $errors[] = "Baris {$baris}: NIK {$row['nik']} sudah terdaftar";
                    $duplikat++;
                    continue;
                }

                if (!empty($row['no_hp']) && Pendaftaran::where('no_hp', $row['no_hp'])->exists()) {
                    $errors[] = "Baris {$baris}: No HP {$row['no_hp']} sudah terdaftar";
                    $duplikat++;
                    continue;
                }

                $programStudi = ProgramStudi::where('nama', 'like', '%' . $row['program_studi'] . '%')->first();

                if (!$programStudi) {
                    $errors[] = "Baris {$baris}: Program studi '{$row['program_studi']}' tidak ditemukan";
                    $gagal++;
                    continue;
                }

                $self = $this;
                $resetEmail = null;

                DB::transaction(function () use (
                    $row,
                    $periodeId,
                    $programStudi,
                    $kriteria,
                    $sawService,
                    $self,
                    &$berhasil,
                    &$resetEmail
                ) {
                    // ===== Buat akun user =====
                    // Email dianggap "asli" jika kolom email diisi di file impor.
                    // Jika kosong -> dibuatkan email placeholder @import.ukt (tidak bisa menerima email).
                    $hasRealEmail = !empty($row['email']);
                    $email = $hasRealEmail
                        ? $row['email']
                        : strtolower(str_replace(' ', '.', $row['nama_lengkap'])) . '@import.ukt';

                    $user = \App\Models\User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name'     => $row['nama_lengkap'],
                            // Tanpa email asli: password awal = NIK, agar mahasiswa tetap bisa login.
                            // Dengan email asli: password acak, mahasiswa set sendiri lewat reset link.
                            'password' => bcrypt($hasRealEmail
                                ? \Illuminate\Support\Str::random(16)
                                : (string) $row['nik']),
                        ]
                    );

                    // Reset link HANYA dikirim jika email asli (email placeholder @import.ukt
                    // tidak akan pernah sampai). Yang tanpa email asli login pakai NIK.
                    // Email dikirim SETELAH transaksi commit (di luar closure) agar kegagalan
                    // pengiriman email tidak ikut membatalkan penyimpanan data mahasiswa.
                    if ($user->wasRecentlyCreated && $hasRealEmail) {
                        $resetEmail = $user->email;
                    }

                    if (!$user->hasRole('mahasiswa')) {
                        $user->assignRole('mahasiswa');
                    }

                    // ===== Nomor pendaftaran =====
                    // Pakai generator terpusat agar konsisten dengan pendaftaran manual
                    // dan ada proteksi anti-duplikat (do-while di dalam generateNomor).
                    $noPendaftaran = Pendaftaran::generateNomor($periodeId);

                    // ===== Parse status KIP =====
                    $isKip = $self->parseBoolean($row['is_kip'] ?? null) ?? false;
                    $noKip = $isKip ? trim((string) ($row['no_kip'] ?? '')) : null;

                    // ===== Simpan pendaftaran (pakai DB::table agar boolean PostgreSQL aman) =====
                    DB::table('pendaftaran')->insert([
                        'user_id'          => $user->id,
                        'periode_id'       => $periodeId,
                        'program_studi_id' => $programStudi->id,
                        'no_pendaftaran'   => $noPendaftaran,
                        'nama_lengkap'     => $row['nama_lengkap'],
                        'nik'              => (string) $row['nik'],
                        'no_hp'            => (string) ($row['no_hp'] ?? '-'),
                        'asal_daerah'      => $row['asal_daerah'] ?? '-',
                        'provinsi'         => $row['provinsi'] ?? '-',
                        'status'           => 'diverifikasi',
                        'is_kip'           => $isKip ? 'true' : 'false',
                        'no_kip'           => $noKip,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);

                    $pendaftaran = Pendaftaran::where('no_pendaftaran', $noPendaftaran)
                        ->where('periode_id', $periodeId)
                        ->firstOrFail();

                    // ===== Simpan data ekonomi (FIX: tidak lagi 0) =====
                    // Nilai penghasilan/tanggungan/pekerjaan/dll diturunkan dari
                    // sub kriteria yang cocok, BUKAN (float) mentah dari label Excel.
                    $resolve  = $self->resolveSubKriteria($row, $kriteria);
                    $fieldEko = $self->petakanFieldEkonomi($resolve['subs']);

                    DataEkonomi::create(array_merge(
                        ['pendaftaran_id' => $pendaftaran->id],
                        $fieldEko,        // penghasilan_ayah/ibu/total, tanggungan, pekerjaan, dll
                        $resolve['ids']   // sub_c1_id ... sub_c12_id
                    ));

                    // ===== Jalankan SAW / bypass KIP =====
                    // SawService::hitung() sekarang mengembalikan array dan SUDAH
                    // menyimpan hasil_saw, detail_saw, dan hasil_ukt secara internal
                    // (baik jalur KIP maupun Non-KIP). Tidak perlu insert manual lagi.
                    // Jangan update ranking per baris (mahal). Ranking dihitung sekali di akhir.
                    $sawService->hitung($pendaftaran, updateRanking: false);

                    $berhasil++;
                });

                // Kirim email reset password DI LUAR transaksi agar gagal email tidak
                // membatalkan data yang sudah tersimpan.
                if ($resetEmail) {
                    try {
                        \Illuminate\Support\Facades\Password::sendResetLink(['email' => $resetEmail]);
                    } catch (\Throwable $mailEx) {
                        Log::warning("Gagal kirim reset link ke {$resetEmail}: " . $mailEx->getMessage());
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Baris {$baris}: " . $e->getMessage();
                $gagal++;
                Log::error("ETL Import Error baris {$baris}: " . $e->getMessage());
            }
        }

        // Update ranking SAW SEKALI saja setelah semua baris diproses (hindari O(n^2)).
        if ($berhasil > 0) {
            try {
                $sawService->updateRankingPeriode($periodeId);
            } catch (\Throwable $rankEx) {
                Log::error("Gagal update ranking periode {$periodeId}: " . $rankEx->getMessage());
            }
        }

        // [TEST CASE] Waktu pemrosesan & rata-rata per baris
        $__totalBaris   = count($this->rows);
        $__detikProses  = round(microtime(true) - $__mulaiProses, 2);
        $__menit        = intdiv((int) $__detikProses, 60);
        $__sisaDetik    = round($__detikProses - ($__menit * 60), 2);
        $__rataPerBaris = $__totalBaris > 0 ? round($__detikProses / $__totalBaris, 4) : 0;
        Log::info("[TEST CASE] Pemrosesan {$__totalBaris} baris: {$__detikProses} detik ({$__menit} menit {$__sisaDetik} detik) | Rata-rata: {$__rataPerBaris} detik/baris");

        $log->update([
            'status'     => 'selesai',
            'berhasil'   => $berhasil,
            'gagal'      => $gagal,
            'duplikat'   => $duplikat,
            'error_log'  => $errors,
            'selesai_at' => now(),
        ]);
    }

    /**
     * Cari sub kriteria yang cocok untuk tiap kolom kriteria.
     * @return array{ids: array<string,int>, subs: array<string,\App\Models\SubKriteria>}
     */
    private function resolveSubKriteria(array $row, $kriteria): array
    {
        $ids  = [];
        $subs = [];

        foreach ($kriteria as $kode => $k) {
            $field = 'sub_' . strtolower($kode) . '_id';
            $kolom = $this->kolomKriteria($k->nama);

            $nilaiRow = $row[$kolom] ?? $row[strtolower($kode)] ?? null;

            if ($nilaiRow === null || trim((string) $nilaiRow) === '') {
                continue;
            }

            $nilaiLower = strtolower(trim((string) $nilaiRow));

            // Cari sub kriteria berdasarkan label (utama) atau nilai numerik
            $sub = $k->subKriteria->first(function ($s) use ($nilaiRow, $nilaiLower) {
                if (strtolower(trim((string) $s->label)) === $nilaiLower) {
                    return true;
                }
                if ($nilaiLower !== '' && str_contains(strtolower((string) $s->label), $nilaiLower)) {
                    return true;
                }
                return is_numeric($nilaiRow) && (float) $s->nilai == (float) $nilaiRow;
            });

            if ($sub) {
                $ids[$field]  = $sub->id;
                $subs[$kode]  = $sub;
            }
        }

        return ['ids' => $ids, 'subs' => $subs];
    }

    private function parseBoolean($value): ?bool
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }
        $val = strtolower(trim((string) $value));
        if (in_array($val, ['1', 'ya', 'yes', 'true'])) return true;
        if (in_array($val, ['0', 'tidak', 'no', 'false'])) return false;
        return null;
    }

    public function failed(\Throwable $e): void
    {
        $this->importLog->update([
            'status'    => 'gagal',
            'error_log' => ['Fatal: ' . $e->getMessage()],
        ]);
    }
}
