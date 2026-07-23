<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilSaw extends Model
{
    protected $table = 'hasil_saw';

    protected $fillable = [
        'pendaftaran_id',
        'periode_id',
        'skor_akhir',
        'ranking',
        'rekomendasi_golongan',
        'dihitung_pada',
    ];

    protected $casts = [
        'skor_akhir'   => 'decimal:6',
        'dihitung_pada' => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeSeleksi::class, 'periode_id');
    }

    public function details()
    {
        return $this->hasMany(DetailSaw::class);
    }

    public function getSkorPresentaseAttribute(): float
    {
        return round((float) $this->skor_akhir * 100, 2);
    }
}
