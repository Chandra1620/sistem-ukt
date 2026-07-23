<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStudi extends Model
{
    protected $table = 'program_studi';

    protected $fillable = [
        'nama',
        'jenjang',
        'akreditasi',
        'akreditasi_id',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function scopeAktif($query)
    {
        return $query->whereRaw('"aktif" IS TRUE');
    }

    public function getNominalKipAttribute(): int
    {
        return match ($this->akreditasi) {
            'Unggul'      => 6000000,
            'Baik Sekali' => 4000000,
            'Baik'        => 2500000,
            default       => 2500000,
        };
    }

    public function getNominalKipFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_kip, 0, ',', '.');
    }

    public function akreditasiData()
    {
        return $this->belongsTo(Akreditasi::class, 'akreditasi_id');
    }

    public function getAkreditasiNamaAttribute(): string
    {
        return $this->akreditasiData?->nama ?? $this->akreditasi ?? '-';
    }
}
