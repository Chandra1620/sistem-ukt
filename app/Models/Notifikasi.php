<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'data',
        'dibaca',
        'dibaca_at',
    ];

    protected $casts = [
        'data'      => 'array',
        'dibaca'    => 'boolean',
        'dibaca_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBelumDibaca($query)
    {
        return $query->whereRaw('"dibaca" = FALSE');
    }

    public function scopeMilik($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function tandaiDibaca(): void
    {
        $this->update([
            'dibaca'    => true,
            'dibaca_at' => now(),
        ]);
    }

    public function getIkonTipeAttribute(): string
    {
        return match ($this->tipe) {
            'dokumen_ditolak' => 'bi-x-circle-fill text-danger',
            'hasil_ukt'       => 'bi-check-circle-fill text-success',
            'banding_selesai' => 'bi-info-circle-fill text-info',
            default           => 'bi-bell-fill text-primary',
        };
    }
}
