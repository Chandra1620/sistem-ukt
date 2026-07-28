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
 * Kirim SATU reset link (untuk akun hasil impor yang punya email asli).
 *
 * Dipisah per-email supaya:
 *  - bisa diproses PARALEL oleh banyak queue worker (konsisten walau ribuan email);
 *  - retry mandiri bila satu email gagal (SMTP timeout / rate limit Gmail);
 *  - kegagalan satu email tidak mempengaruhi email lain.
 */
class KirimResetLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * Jeda antar percobaan (detik) untuk menghindari rate limit SMTP.
     *
     * @return array<int,int>
     */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function __construct(public string $email) {}

    public function handle(): void
    {
        try {
            Password::sendResetLink(['email' => $this->email]);
        } catch (\Throwable $e) {
            Log::warning("Gagal kirim reset link ke {$this->email}: " . $e->getMessage());
            // Lempar ulang agar job di-retry sesuai backoff().
            throw $e;
        }
    }
}
