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
        $ok = 0;
        $gagal = 0;
        foreach ($this->emails as $email) {
            try {
                Password::sendResetLink(['email' => $email]);
                $ok++;
            } catch (\Throwable $e) {
                $gagal++;
                Log::warning("Gagal kirim reset link ke {$email}: " . $e->getMessage());
            }
        }
        Log::info("[ETL][RESET] Reset link terkirim={$ok}, gagal={$gagal}");
    }
}
