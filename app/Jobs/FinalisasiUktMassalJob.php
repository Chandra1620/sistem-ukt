<?php

namespace App\Jobs;

use App\Mail\HasilUktMail;
use App\Models\HasilUkt;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\RiwayatStatus;
use App\Services\AuditLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class FinalisasiUktMassalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 600;

    /**
     * @param int|null $periodeId ID Periode Seleksi (opsional)
     * @param int|null $userId ID Panitia yang mengeksekusi
     * @param int $chunkSize Ukuran per chunk (default 50)
     */
    public function __construct(
        public ?int $periodeId = null,
        public ?int $userId = null,
        public int $chunkSize = 50
    ) {}

    public function handle(): void
    {
        // 1. Ambil seluruh ID pendaftaran yang berstatus 'hasil_awal'
        $pendaftaranIds = Pendaftaran::where('status', 'hasil_awal')
            ->when($this->periodeId, fn($q) => $q->where('periode_id', $this->periodeId))
            ->pluck('id');

        if ($pendaftaranIds->isEmpty()) {
            Log::info("FinalisasiUktMassalJob: Tidak ada pendaftaran berstatus hasil_awal yang perlu difinalisasi.");
            return;
        }

        $totalBerhasil = 0;
        $chunkIndex = 0;

        // 2. Proses per chunk kecil (misal 50), satu transaksi per chunk
        foreach ($pendaftaranIds->chunk($this->chunkSize) as $chunkIds) {
            $chunkIndex++;

            try {
                $berhasilChunk = DB::transaction(function () use ($chunkIds) {
                    $now = now();

                    // Kunci baris hanya dalam chunk ini dan pastikan status masih 'hasil_awal' (anti finalisasi ganda)
                    $pendaftarans = Pendaftaran::whereIn('id', $chunkIds)
                        ->where('status', 'hasil_awal')
                        ->with(['hasilUkt', 'user'])
                        ->lockForUpdate()
                        ->get();

                    // Pastikan hasilUkt ada
                    $eligible = $pendaftarans->filter(fn($p) => $p->hasilUkt !== null);

                    if ($eligible->isEmpty()) {
                        return 0;
                    }

                    $eligibleIds = $eligible->pluck('id')->all();

                    // a. Update status pendaftaran secara massal (1 query)
                    Pendaftaran::whereIn('id', $eligibleIds)
                        ->where('status', 'hasil_awal')
                        ->update([
                            'status'     => 'final_ukt',
                            'updated_at' => $now,
                        ]);

                    // b. Update hasil_ukt secara massal (1 query)
                    HasilUkt::whereIn('pendaftaran_id', $eligibleIds)
                        ->update([
                            'finalisasi_oleh' => $this->userId,
                            'finalisasi_at'   => $now,
                            'updated_at'      => $now,
                        ]);

                    // c. Siapkan data log & notifikasi untuk bulk insert
                    $riwayatRows = [];
                    $notifikasiRows = [];
                    $emailPayloads = [];

                    foreach ($eligible as $pendaftaran) {
                        $riwayatRows[] = [
                            'pendaftaran_id' => $pendaftaran->id,
                            'status_lama'    => 'hasil_awal',
                            'status_baru'    => 'final_ukt',
                            'keterangan'     => 'UKT difinalisasi massal oleh panitia',
                            'diubah_oleh'    => $this->userId,
                            'created_at'     => $now,
                            'updated_at'     => $now,
                        ];

                        $hasilUkt = $pendaftaran->hasilUkt;
                        $nominal = (float) $hasilUkt->nominal_final;
                        $golongan = (int) $hasilUkt->golongan_final;
                        $nominalFormat = 'Rp ' . number_format($nominal, 0, ',', '.');

                        $notifikasiRows[] = [
                            'user_id'    => $pendaftaran->user_id,
                            'judul'      => 'UKT Telah Difinalisasi',
                            'pesan'      => "UKT Anda telah difinalisasi: Golongan {$golongan} ({$nominalFormat})",
                            'tipe'       => 'hasil_ukt',
                            'data'       => json_encode([
                                'pendaftaran_id' => $pendaftaran->id,
                                'golongan'       => $golongan,
                            ]),
                            'dibaca'     => false,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        if ($pendaftaran->user?->email) {
                            $emailPayloads[] = [
                                'email'       => $pendaftaran->user->email,
                                'pendaftaran' => $pendaftaran,
                                'golongan'    => $golongan,
                                'nominal'     => $nominal,
                            ];
                        }
                    }

                    // d. Catat log dan notifikasi dengan insert massal (bukan create per baris)
                    RiwayatStatus::insert($riwayatRows);
                    Notifikasi::insert($notifikasiRows);

                    // e. Dispatch email di luar transaksi setelah commit (anti dirty-read & anti phantom mail jika rollback)
                    DB::afterCommit(function () use ($emailPayloads) {
                        foreach ($emailPayloads as $payload) {
                            try {
                                Mail::to($payload['email'])->queue(
                                    new HasilUktMail(
                                        $payload['pendaftaran'],
                                        $payload['golongan'],
                                        $payload['nominal']
                                    )
                                );
                            } catch (Throwable $mailEx) {
                                Log::error("Gagal antre email UKT pendaftaran #{$payload['pendaftaran']->id}: " . $mailEx->getMessage());
                            }
                        }
                    });

                    return count($eligibleIds);
                });

                $totalBerhasil += $berhasilChunk;
            } catch (Throwable $e) {
                Log::error("Gagal memproses finalisasi massal pada chunk ke-{$chunkIndex}: " . $e->getMessage(), [
                    'periode_id' => $this->periodeId,
                    'chunk_ids'  => $chunkIds->all(),
                    'exception'  => $e,
                ]);

                // Lempar kembali exception agar sistem queue mencatat kegagalan dan dapat melakukan retry otomatis
                throw $e;
            }
        }

        // 3. Catat audit log setelah semua chunk selesai
        if ($totalBerhasil > 0) {
            AuditLogService::log(
                'finalisasi_massal',
                'Pendaftaran',
                null,
                null,
                ['jumlah' => $totalBerhasil, 'periode_id' => $this->periodeId],
                $this->userId
            );
        }

        Log::info("FinalisasiUktMassalJob selesai: {$totalBerhasil} mahasiswa berhasil difinalisasi.");
    }
}
