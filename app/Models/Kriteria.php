<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'kriteria';

    protected $fillable = [
        'nama',
        'kode',
        'bobot',
        'tipe',
        'periode_id',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodeSeleksi::class, 'periode_id');
    }

    public function subKriteria()
    {
        return $this->hasMany(SubKriteria::class);
    }

    public function detailSaw()
    {
        return $this->hasMany(DetailSaw::class);
    }
}
