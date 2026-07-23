<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    protected $table = 'dokumen';

    protected $fillable = [
        'pendaftaran_id',
        'jenis_dokumen_id',
        'jenis',
        'nama_file',
        'path',
        'mime_type',
        'ukuran',
        'status',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumen::class);
    }

    public function verifikasi()
    {
        return $this->hasOne(VerifikasiDokumen::class);
    }

    public function getNamaJenisAttribute(): string
    {
        return $this->jenisDokumen?->nama ?? $this->jenis ?? '-';
    }
}
