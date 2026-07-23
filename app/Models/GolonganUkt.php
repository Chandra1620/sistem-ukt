<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GolonganUkt extends Model
{
    protected $table = 'golongan_ukt';

    protected $fillable = [
        'golongan',
        'nominal',
        'skor_min',
        'skor_max',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'skor_min' => 'decimal:4',
        'skor_max' => 'decimal:4',
    ];

    public function hasilUkt()
    {
        return $this->hasMany(HasilUkt::class, 'golongan_final', 'golongan');
    }

    public static function getNominalByGolongan(int $golongan): float
    {
        return self::where('golongan', $golongan)->value('nominal') ?? 0;
    }
}
