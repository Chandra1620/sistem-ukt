<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PeriodeSeleksi extends Model
{
    protected $table = 'periode_seleksi';

    protected $fillable = [
        'nama',
        'tahun',
        'tanggal_mulai',
        'tanggal_selesai',
        'status'
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function getStatusRealAttribute(): string
    {
        if ($this->status === 'nonaktif') return 'nonaktif';

        $now = Carbon::now();

        if ($now->gt($this->tanggal_selesai)) {
            return 'selesai';
        }

        if ($now->lt($this->tanggal_mulai)) {
            return 'belum_mulai';
        }

        return $this->status;
    }

    public function getIsAktifAttribute(): bool
    {
        return $this->status === 'aktif'
            && Carbon::now()->between($this->tanggal_mulai, $this->tanggal_selesai);
    }

    public function getSisaHariAttribute(): int
    {
        return max(0, Carbon::now()->diffInDays($this->tanggal_selesai, false));
    }

    public function kriteria()
    {
        return $this->hasMany(Kriteria::class, 'periode_id');
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class, 'periode_id');
    }

    // Auto update status saat diakses
    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($periode) {
            if ($periode->status === 'aktif' && Carbon::now()->gt($periode->tanggal_selesai)) {
                $periode->updateQuietly(['status' => 'selesai']);
            }
        });
    }
}
