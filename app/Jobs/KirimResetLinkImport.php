<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

/**
 * Kirim reset link untuk akun hasil impor secara ASYNC.
 * Dipisah dari ProsesImportEtl supaya proses impor tidak menunggu pengiriman
 * (untuk ratusan email bisa lama, apalagi via SMTP).
 */
class KirimResetLinkImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 1800;

    public function __construct(public array $emails) {}

    public function handle(): void
    {
        // Fan-out: tiap email menjadi job tersendiri agar bisa diproses PARALEL
        // oleh banyak worker, retry mandiri saat SMTP gagal/kena limit, dan
        // kegagalan satu email tidak menggagalkan yang lain. Job ini sendiri
        // selesai seketika (hanya menjadwalkan), jadi tidak ada loop menunggu.
        foreach ($this->emails as $email) {
            KirimResetLink::dispatch($email);
        }
        Log::info('[ETL][RESET] ' . count($this->emails) . ' job kirim reset link dijadwalkan (paralel per-email).');
    }
}
