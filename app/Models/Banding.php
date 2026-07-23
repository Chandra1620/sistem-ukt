<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banding extends Model
{
    protected $table = 'banding';

    protected $fillable = [
        'pendaftaran_id',
        'hasil_ukt_id',
        'alasan',
        'status',
        'keputusan_panitia',
        'golongan_baru',
        'nominal_baru',
        'diproses_oleh',
        'diproses_at',
    ];

    protected $casts = [
        'nominal_baru' => 'decimal:2',
        'diproses_at'  => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function hasilUkt()
    {
        return $this->belongsTo(HasilUkt::class);
    }

    public function diprosesOleh()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'diajukan'  => 'Diajukan',
            'diproses'  => 'Sedang Diproses',
            'diterima'  => 'Diterima',
            'ditolak'   => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }

    public function getWarnaBadgeAttribute(): string
    {
        return match ($this->status) {
            'diajukan'  => 'warning',
            'diproses'  => 'info',
            'diterima'  => 'success',
            'ditolak'   => 'danger',
            default     => 'secondary',
        };
    }
}
