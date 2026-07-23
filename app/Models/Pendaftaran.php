<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    protected $fillable = [
        'user_id',
        'periode_id',
        'program_studi_id',
        'no_pendaftaran',
        'status',
        'nama_lengkap',
        'nik',
        'no_hp',
        'asal_daerah',
        'provinsi',
        'is_kip',
        'no_kip',
    ];

    protected $casts = [
        'is_kip' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeSeleksi::class, 'periode_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    public function dataEkonomi()
    {
        return $this->hasOne(DataEkonomi::class);
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }

    public function verifikasi()
    {
        return $this->hasMany(VerifikasiDokumen::class);
    }

    public function hasilSaw()
    {
        return $this->hasOne(HasilSaw::class);
    }

    public function hasilUkt()
    {
        return $this->hasOne(HasilUkt::class);
    }

    public function banding()
    {
        return $this->hasMany(Banding::class);
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatus::class);
    }

    public function bisaDiedit(): bool
    {
        return in_array($this->status, ['draft', 'ditolak_reviewer']);
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'draft'                => 'Draft',
            'menunggu_verifikasi'  => 'Menunggu Verifikasi',
            'ditolak_reviewer'     => 'Ditolak Reviewer',
            'diverifikasi'         => 'Diverifikasi',
            'sedang_dihitung'      => 'Sedang Dihitung',
            'hasil_awal'           => 'Hasil Awal',
            'final_ukt'            => 'Final UKT',
            'banding_diproses'     => 'Banding Diproses',
            'selesai'              => 'Selesai',
            default                => ucfirst($this->status),
        };
    }

    public function getWarnaBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'                => 'secondary',
            'menunggu_verifikasi'  => 'warning',
            'ditolak_reviewer'     => 'danger',
            'diverifikasi'         => 'info',
            'sedang_dihitung'      => 'primary',
            'hasil_awal'           => 'primary',
            'final_ukt'            => 'success',
            'banding_diproses'     => 'warning',
            'selesai'              => 'success',
            default                => 'secondary',
        };
    }

    public static function generateNomor(int $periodeId): string
    {
        $tahun = date('Y');

        return DB::transaction(function () use ($tahun) {
            // Cari nomor tertinggi secara GLOBAL untuk tahun ini (lintas periode),
            // karena no_pendaftaran harus unik di seluruh tabel, bukan cuma per periode.
            // lockForUpdate() mencegah race condition kalau ada 2 insert bersamaan.
            $last = self::where('no_pendaftaran', 'like', "UKT-{$tahun}-%")
                ->lockForUpdate()
                ->orderByDesc('no_pendaftaran')
                ->value('no_pendaftaran');

            $nextNum = $last ? ((int) substr($last, -5)) + 1 : 1;

            return 'UKT-' . $tahun . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
        });
    }
}
