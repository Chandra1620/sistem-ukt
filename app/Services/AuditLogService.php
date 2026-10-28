<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public static function log(
        string $aksi,
        ?string $model = null,
        ?int $modelId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null,
        ?int $userId = null
    ): void {
        $now = now();
        AuditLog::create([
            'user_id'    => $userId ?? Auth::id(),
            'aksi'       => $aksi,
            'model'      => $model,
            'model_id'   => $modelId,
            'data_lama'  => $dataLama,
            'data_baru'  => $dataBaru,
            'ip_address' => request()?->ip(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Catat banyak log sekaligus (bulk insert) dengan memastikan created_at & updated_at selalu terisi.
     */
    public static function logMassal(array $records): void
    {
        if (empty($records)) {
            return;
        }

        $now = now();
        $defaultIp = request()?->ip();
        $defaultUserId = Auth::id();

        $rows = array_map(function ($record) use ($now, $defaultIp, $defaultUserId) {
            return [
                'user_id'    => $record['user_id'] ?? $defaultUserId,
                'aksi'       => $record['aksi'],
                'model'      => $record['model'] ?? null,
                'model_id'   => $record['model_id'] ?? null,
                'data_lama'  => isset($record['data_lama']) ? json_encode($record['data_lama']) : null,
                'data_baru'  => isset($record['data_baru']) ? json_encode($record['data_baru']) : null,
                'ip_address' => $record['ip_address'] ?? $defaultIp,
                'created_at' => $record['created_at'] ?? $now,
                'updated_at' => $record['updated_at'] ?? $now,
            ];
        }, $records);

        foreach (array_chunk($rows, 500) as $chunk) {
            AuditLog::insert($chunk);
        }
    }
}
