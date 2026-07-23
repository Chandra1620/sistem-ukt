<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HasilUkt extends Model
{
    protected $table = 'hasil_ukt';

    protected $fillable = [
        'pendaftaran_id',
        'golongan_rekomendasi',
        'golongan_final',
        'nominal_final',
        'sumber_pembiayaan',
        'dasar_penetapan',
        'is_override',
        'alasan_override',
        'override_oleh',
        'override_at',
        'finalisasi_oleh',
        'finalisasi_at',
    ];

    protected $casts = [
        'nominal_final' => 'decimal:2',
        'is_override'   => 'boolean',
        'override_at'   => 'datetime',
        'finalisasi_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (isset($model->is_override)) {
                $model->is_override = $model->is_override ? DB::raw('TRUE') : DB::raw('FALSE');
            }
        });

        static::updating(function ($model) {
            if (isset($model->is_override)) {
                $model->is_override = $model->is_override ? DB::raw('TRUE') : DB::raw('FALSE');
            }
        });
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }


    public function overrideOleh()
    {
        return $this->belongsTo(User::class, 'override_oleh');
    }

    public function finalisasiOleh()
    {
        return $this->belongsTo(User::class, 'finalisasi_oleh');
    }

    public function banding()
    {
        return $this->hasMany(Banding::class);
    }

    public function getNominalFormatAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->nominal_final, 0, ',', '.');
    }

    public function golonganUkt()
    {
        return $this->belongsTo(GolonganUkt::class, 'golongan_final', 'golongan');
    }

    public static function getNominalByGolongan(int $golongan): float
    {
        $g = GolonganUkt::where('golongan', $golongan)->first();
        return $g ? (float) $g->nominal : 0;
    }
}
