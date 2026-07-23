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
        ?array $dataBaru = null
    ): void {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'aksi'       => $aksi,
            'model'      => $model,
            'model_id'   => $modelId,
            'data_lama'  => $dataLama,
            'data_baru'  => $dataBaru,
            'ip_address' => request()->ip(),
        ]);
    }
}
