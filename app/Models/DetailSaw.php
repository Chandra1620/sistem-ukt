<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailSaw extends Model
{
    protected $table = 'detail_saw';

    protected $fillable = [
        'hasil_saw_id',
        'kriteria_id',
        'nilai_asli',
        'nilai_normalisasi',
        'nilai_terbobot',
    ];

    protected $casts = [
        'nilai_asli'         => 'decimal:4',
        'nilai_normalisasi'  => 'decimal:6',
        'nilai_terbobot'     => 'decimal:6',
    ];

    public function hasilSaw()
    {
        return $this->belongsTo(HasilSaw::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
