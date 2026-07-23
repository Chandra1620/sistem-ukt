<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Akreditasi extends Model
{
    protected $table = 'akreditasi';

    protected $fillable = [
        'nama',
        'nominal_kip',
        'keterangan',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif'      => 'boolean',
        'nominal_kip' => 'decimal:2',
    ];

    public function programStudi()
    {
        return $this->hasMany(ProgramStudi::class);
    }

    public function getNominalKipFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_kip, 0, ',', '.');
    }

    public function scopeAktif($query)
    {
        return $query->whereRaw('"aktif" IS TRUE');
    }
}
