<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiDokumen extends Model
{
    protected $table = 'verifikasi_dokumen';

    protected $fillable = [
        'pendaftaran_id',
        'reviewer_id',
        'hasil',
        'komentar',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getLabelHasilAttribute(): string
    {
        return match ($this->hasil) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => ucfirst($this->hasil),
        };
    }

    public function getWarnaBadgeAttribute(): string
    {
        return match ($this->hasil) {
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'secondary',
        };
    }
}
