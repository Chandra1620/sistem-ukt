<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisDokumen extends Model
{
    protected $table = 'jenis_dokumen';

    protected $fillable = [
        'nama',
        'keterangan',
        'wajib',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'wajib' => 'boolean',
        'aktif' => 'boolean',
    ];

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }

    public function scopeAktif($query)
    {
        return $query->whereRaw('"aktif" IS TRUE');
    }

    public function scopeWajib($query)
    {
        return $query->whereRaw('"wajib" IS TRUE');
    }
    
}
