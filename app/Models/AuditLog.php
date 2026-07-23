<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';

    protected $fillable = [
        'user_id',
        'aksi',
        'model',
        'model_id',
        'data_lama',
        'data_baru',
        'ip_address',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLabelAksiAttribute(): string
    {
        return match ($this->aksi) {
            'override_ukt'   => 'Override UKT',
            'ubah_bobot'     => 'Ubah Bobot Kriteria',
            'finalisasi_ukt' => 'Finalisasi UKT',
            'proses_banding' => 'Proses Banding',
            'ubah_periode'   => 'Ubah Periode Seleksi',
            'tambah_user'    => 'Tambah User',
            'ubah_role'      => 'Ubah Role User',
            default          => ucfirst(str_replace('_', ' ', $this->aksi)),
        };
    }
}
