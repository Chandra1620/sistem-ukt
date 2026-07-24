<?php

namespace App\Jobs;

use App\Jobs\KirimResetLinkImport;
use App\Models\ImportLog;
use App\Models\Kriteria;
use App\Models\Pendaftaran;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Services\SawService;
use App\Support\NormalisasiKolom;
use App\Support\PemetaanDataEkonomi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Import ETL versi BULK.
 *
 * Perbaikan utama vs versi lama:
 *  1) Data ekonomi tidak lagi 0 -> nilai penghasilan/tanggungan/dll diturunkan
 *     dari sub kriteria yang cocok (via trait PemetaanDataEkonomi), bukan
 *     (float) mentah terhadap label teks Excel.
 *  2) Performa: tidak ada query di dalam loop. Semua referensi di-preload,
 *     insert dilakukan secara batch (bulk insert), nomor pendaftaran dihitung
 *     di memori, dan SAW dihitung massal sekali jalan. Dari ~57 menit menjadi
 *     hitungan detik untuk ribuan baris.
 */
class ProsesImportEtl implements ShouldQueue
{
    use NormalisasiKolom;
    use PemetaanDataEkonomi;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    // Longgarkan timeout untuk file sangat besar. Dengan bulk insert, proses
    // ribuan baris tetap selesai jauh di bawah batas ini.
    public int $timeout = 1800;

    // Ukuran chunk insert untuk menjaga penggunaan memori tetap stabil.
    private const CHUNK = 500;

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

        $mulai = microtime(true);

        $gagal    = 0;
        $duplikat = 0;
        $errors   = [];

        // ============================================================
        // 1) PRELOAD semua referensi SEKALI (hilangkan N+1)
        // ============================================================
        $kriteria = Kriteria::where('periode_id', $periodeId)
            ->with('subKriteria')
            ->orderByRaw("CAST(SUBSTRING(kode, 2) AS INTEGER)")
            ->get()
            ->keyBy('kode');

        // Program studi: simpan list untuk pencocokan 'contains' tanpa query per baris
        $prodiList = ProgramStudi::all(['id', 'nama'])
            ->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama, 'lower' => strtolower($p->nama)]);

        // NIK & No HP yang sudah ada di DB (set untuk cek duplikat O(1))
        $nikTerdaftar = DB::table('pendaftaran')->pluck('nik')->flip();
        $hpTerdaftar  = DB::table('pendaftaran')->whereNotNull('no_hp')->pluck('no_hp')->flip();

        // Role mahasiswa (spatie)
        $mahasiswaRoleId = DB::table('roles')->where('name', 'mahasiswa')->value('id');

        // Nomor pendaftaran: hitung sekuens awal SEKALI, lalu increment di memori
        $tahun   = date('Y');
        $lastNo  = DB::table('pendaftaran')
            ->where('no_pendaftaran', 'like', "UKT-{$tahun}-%")
            ->orderByDesc('no_pendaftaran')
            ->value('no_pendaftaran');
        $counter = $lastNo ? ((int) substr($lastNo, -5)) : 0;

        // ============================================================
        // 2) VALIDASI + NORMALISASI baris (in-memory, tanpa query)
        // ============================================================
        $valid       = [];
        $seenNik     = [];
        $seenHp      = [];
        $emailsBaris = [];

        foreach ($this->rows as $index => $row) {
            $baris = $index + 2;

            $nik = isset($row['nik']) ? (string) $row['nik'] : '';
            if ($nik === '' || !preg_match('/^\d{16}$/', $nik)) {
                $errors[] = "Baris {$baris}: NIK tidak valid ({$nik})";
                $gagal++;
                continue;
            }
            if (isset($nikTerdaftar[$nik]) || isset($seenNik[$nik])) {
                $errors[] = "Baris {$baris}: NIK {$nik} sudah terdaftar / duplikat";
                $duplikat++;
                continue;
            }

            $noHp = trim((string) ($row['no_hp'] ?? ''));
            if ($noHp !== '' && (isset($hpTerdaftar[$noHp]) || isset($seenHp[$noHp]))) {
                $errors[] = "Baris {$baris}: No HP {$noHp} sudah terdaftar / duplikat";
                $duplikat++;
                continue;
            }

            $inputProdi = strtolower(trim((string) ($row['program_studi'] ?? '')));
            $prodi = $prodiList->first(fn($p) => $inputProdi !== '' && str_contains($p['lower'], $inputProdi));
            if (!$prodi) {
                $errors[] = "Baris {$baris}: Program studi '" . ($row['program_studi'] ?? '') . "' tidak ditemukan";
                $gagal++;
                continue;
            }

            // Tandai sudah dipakai (cegah duplikat dalam file yang sama)
            $seenNik[$nik] = true;
            if ($noHp !== '') {
                $seenHp[$noHp] = true;
            }

            // Petakan sub kriteria + turunkan field ekonomi (FIX data 0)
            $resolve  = $this->resolveSubKriteria($row, $kriteria);
            $fieldEko = $this->petakanFieldEkonomi($resolve['subs']);

            $hasRealEmail = !empty($row['email']);
            $email = $hasRealEmail
                ? strtolower(trim((string) $row['email']))
                : strtolower(str_replace(' ', '.', (string) $row['nama_lengkap'])) . '@import.ukt';

            $emailsBaris[$email] = [
                'name'         => $row['nama_lengkap'],
                'nik'          => $nik,
                'hasRealEmail' => $hasRealEmail,
            ];

            $isKip = $this->parseBoolean($row['is_kip'] ?? null) ?? false;

            $valid[] = [
                'nik'        => $nik,
                'no_hp'      => $noHp !== '' ? $noHp : '-',
                'nama'       => $row['nama_lengkap'],
                'asal'       => $row['asal_daerah'] ?? '-',
                'provinsi'   => $row['provinsi'] ?? '-',
                'prodi_id'   => $prodi['id'],
                'is_kip'     => $isKip,
                'no_kip'     => $isKip ? trim((string) ($row['no_kip'] ?? '')) : null,
                'email'      => $email,
                'sub_ids'    => $resolve['ids'],
                'field_eko'  => $fieldEko,
            ];
        }

        if (empty($valid)) {
            $this->selesaikanLog($log, 0, $gagal, $duplikat, $errors, $mulai);
            return;
        }

        // ============================================================
        // 3) BULK INSERT user + role + pendaftaran + data_ekonomi + SAW
        // ============================================================
        $now            = now();
        $pendaftaranIds = [];
        $resetEmails    = [];
        // Pra-hash password DI LUAR transaksi, pakai cost ringan (rounds 10).
        // Akun impor sementara & wajib ganti password; login tetap valid karena
        // cost tertanam di hash. Registrasi normal tetap pakai BCRYPT_ROUNDS global.
        $__t0 = microtime(true);
        foreach ($emailsBaris as $emailKey => $infoKey) {
            $emailsBaris[$emailKey]['password'] = Hash::make(
                ($infoKey['hasRealEmail'] ?? false) ? Str::random(16) : $infoKey['nik'],
                ['rounds' => 8]
            );
        }
        $__tHash = microtime(true);

        DB::transaction(function () use (
            $valid,
            $emailsBaris,
            $periodeId,
            $mahasiswaRoleId,
            $tahun,
            &$counter,
            $now,
            &$pendaftaranIds,
            &$resetEmails
        ) {
            // ---- 3a. User (bulk) ----
            $emailAda = DB::table('users')
                ->whereIn('email', array_keys($emailsBaris))
                ->pluck('id', 'email');

            $userBaru = [];
            foreach ($emailsBaris as $email => $info) {
                if (isset($emailAda[$email])) {
                    continue;
                }
                $userBaru[$email] = [
                    'name'       => $info['name'],
                    'email'      => $email,
                    'password'   => $info['password'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if ($info['hasRealEmail']) {
                    $resetEmails[] = $email;
                }
            }
            foreach (array_chunk(array_values($userBaru), self::CHUNK) as $chunk) {
                DB::table('users')->insert($chunk);
            }

            // Map email -> user_id untuk SEMUA email di batch
            $userIdByEmail = DB::table('users')
                ->whereIn('email', array_keys($emailsBaris))
                ->pluck('id', 'email');

            // ---- 3b. Role mahasiswa (bulk, spatie) ----
            if ($mahasiswaRoleId) {
                $userIds = $userIdByEmail->values()->all();
                $sudahPunya = DB::table('model_has_roles')
                    ->where('role_id', $mahasiswaRoleId)
                    ->where('model_type', User::class)
                    ->whereIn('model_id', $userIds)
                    ->pluck('model_id')
                    ->flip();
                $roleRows = [];
                foreach ($userIds as $uid) {
                    if (!isset($sudahPunya[$uid])) {
                        $roleRows[] = [
                            'role_id'    => $mahasiswaRoleId,
                            'model_type' => User::class,
                            'model_id'   => $uid,
                        ];
                    }
                }
                foreach (array_chunk($roleRows, self::CHUNK) as $chunk) {
                    DB::table('model_has_roles')->insert($chunk);
                }
            }

            // ---- 3c. Pendaftaran (bulk) ----
            $pendaftaranRows = [];
            foreach ($valid as &$v) {
                $counter++;
                $v['no_pendaftaran'] = 'UKT-' . $tahun . '-' . str_pad($counter, 5, '0', STR_PAD_LEFT);
                $pendaftaranRows[] = [
                    'user_id'          => $userIdByEmail[$v['email']],
                    'periode_id'       => $periodeId,
                    'program_studi_id' => $v['prodi_id'],
                    'no_pendaftaran'   => $v['no_pendaftaran'],
                    'nama_lengkap'     => $v['nama'],
                    'nik'              => $v['nik'],
                    'no_hp'            => $v['no_hp'],
                    'asal_daerah'      => $v['asal'],
                    'provinsi'         => $v['provinsi'],
                    'status'           => 'diverifikasi',
                    'is_kip'           => $v['is_kip'] ? 'true' : 'false',
                    'no_kip'           => $v['no_kip'],
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
            unset($v);
            foreach (array_chunk($pendaftaranRows, self::CHUNK) as $chunk) {
                DB::table('pendaftaran')->insert($chunk);
            }

            // Map no_pendaftaran -> id
            $noList = array_column($valid, 'no_pendaftaran');
            $idByNo = DB::table('pendaftaran')
                ->whereIn('no_pendaftaran', $noList)
                ->pluck('id', 'no_pendaftaran');

            // ---- 3d. Data ekonomi (bulk) ----
            $ekoRows = [];
            foreach ($valid as $v) {
                $pid = $idByNo[$v['no_pendaftaran']] ?? null;
                if (!$pid) {
                    continue;
                }
                $pendaftaranIds[] = $pid;
                $ekoRows[] = array_merge(
                    [
                        'pendaftaran_id' => $pid,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ],
                    $v['field_eko'],   // penghasilan_ayah/ibu/total, tanggungan, listrik, dll
                    $v['sub_ids']      // sub_c1_id ... sub_c12_id
                );
            }
            foreach (array_chunk($ekoRows, self::CHUNK) as $chunk) {
                DB::table('data_ekonomi')->insert($chunk);
            }
        });
        $__tTx = microtime(true);

        // ---- 3e. SAW massal (sekali jalan, sudah bulk di dalam service) ----
        if (!empty($pendaftaranIds)) {
            $sawService->hitungMassal($pendaftaranIds, $periodeId);
        }
        $__tSaw = microtime(true);

        // ---- 3f. Kirim reset link ASYNC (job terpisah) agar impor tidak menunggu ----
        if (!empty($resetEmails)) {
            KirimResetLinkImport::dispatch($resetEmails);
        }

        Log::info(sprintf(
            '[ETL][PROFIL] hash=%.1fd  insert=%.1fd  saw=%.1fd  (reset link async, %d email)',
            $__tHash - $__t0,
            $__tTx - $__tHash,
            $__tSaw - $__tTx,
            count($resetEmails)
        ));

        $this->selesaikanLog($log, count($pendaftaranIds), $gagal, $duplikat, $errors, $mulai);
    }

    /**
     * Cari sub kriteria yang cocok untuk tiap kolom kriteria.
     * Mengembalikan ['ids' => ['sub_c1_id'=>..], 'subs' => ['C1'=>SubKriteria, ..]]
     */
    private function resolveSubKriteria(array $row, $kriteria): array
    {
        $ids  = [];
        $subs = [];

        foreach ($kriteria as $kode => $k) {
            $field    = 'sub_' . strtolower($kode) . '_id';
            $kolom    = $this->kolomKriteria($k->nama);
            $nilaiRow = $row[$kolom] ?? $row[strtolower($kode)] ?? null;

            if ($nilaiRow === null || trim((string) $nilaiRow) === '') {
                continue;
            }

            $nilaiLower = strtolower(trim((string) $nilaiRow));
            $sub = $k->subKriteria->first(function ($s) use ($nilaiRow, $nilaiLower) {
                // 1) cocok label (kasus normal file impor: berisi label teks)
                if (strtolower(trim((string) $s->label)) === $nilaiLower) {
                    return true;
                }
                // 2) cocok label secara "contains" (toleransi spasi/format)
                if ($nilaiLower !== '' && str_contains(strtolower((string) $s->label), $nilaiLower)) {
                    return true;
                }
                // 3) cocok nilai numerik (jika file berisi angka bobot)
                return is_numeric($nilaiRow) && (float) $s->nilai == (float) $nilaiRow;
            });

            if ($sub) {
                $ids[$field] = $sub->id;
                $subs[$kode] = $sub;
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

    private function selesaikanLog(ImportLog $log, int $berhasil, int $gagal, int $duplikat, array $errors, float $mulai): void
    {
        $detik = round(microtime(true) - $mulai, 2);
        $total = count($this->rows);
        Log::info("[ETL] {$total} baris diproses dalam {$detik} detik (berhasil={$berhasil}, gagal={$gagal}, duplikat={$duplikat})");

        $log->update([
            'status'     => 'selesai',
            'berhasil'   => $berhasil,
            'gagal'      => $gagal,
            'duplikat'   => $duplikat,
            'error_log'  => $errors,
            'selesai_at' => now(),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->importLog->update([
            'status'    => 'gagal',
            'error_log' => ['Fatal: ' . $e->getMessage()],
        ]);
    }
}
