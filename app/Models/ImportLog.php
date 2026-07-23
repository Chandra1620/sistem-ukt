<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $table = 'import_logs';

    protected $fillable = [
        'user_id',
        'nama_file',
        'periode_nama',
        'total_baris',
        'berhasil',
        'gagal',
        'duplikat',
        'status',
        'error_log',
        'selesai_at',
    ];

    protected $casts = [
        'error_log'  => 'array',
        'selesai_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Menunggu',
            'processing' => 'Diproses',
            'selesai'    => 'Selesai',
            'gagal'      => 'Gagal',
            default      => 'Unknown',
        };
    }

    public function getWarnaStatusAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'processing' => 'info',
            'selesai'    => 'success',
            'gagal'      => 'danger',
            default      => 'secondary',
        };
    }
}
