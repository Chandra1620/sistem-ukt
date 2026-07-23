<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    protected $table = 'riwayat_status';

    protected $fillable = [
        'pendaftaran_id',
        'status_lama',
        'status_baru',
        'keterangan',
        'diubah_oleh',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function diubahOleh()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    public function getWarnaBadgeAttribute(): string
    {
        return match ($this->status_baru) {
            'draft'                => 'secondary',
            'menunggu_verifikasi'  => 'warning',
            'ditolak_reviewer'     => 'danger',
            'diverifikasi'         => 'info',
            'sedang_dihitung'      => 'primary',
            'hasil_awal'           => 'primary',
            'final_ukt'            => 'success',
            'banding_diproses'     => 'warning',
            'selesai'              => 'success',
            default                => 'secondary',
        };
    }
}
