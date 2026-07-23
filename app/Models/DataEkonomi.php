<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataEkonomi extends Model
{
    protected $table = 'data_ekonomi';

    protected $fillable = [
        'pendaftaran_id',
        'penghasilan_ayah',
        'penghasilan_ibu',
        'penghasilan_total',
        'sub_c1_id',
        'sub_c2_id',
        'sub_c3_id',
        'sub_c4_id',
        'sub_c5_id',
        'sub_c6_id',
        'sub_c7_id',
        'sub_c8_id',
        'sub_c9_id',
        'sub_c10_id',
        'sub_c11_id',
        'sub_c12_id',
        'jumlah_tanggungan',
        'kepemilikan_rumah',
        'luas_tanah',
        'pbb',
        'hutang_cicilan',
        'tagihan_listrik',
        'tagihan_air',
        'kepemilikan_kendaraan',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
    ];

    protected $casts = [
        'penghasilan_ayah'  => 'decimal:2',
        'penghasilan_ibu'   => 'decimal:2',
        'penghasilan_total' => 'decimal:2',
        'luas_tanah'        => 'decimal:2',
        'pbb'               => 'decimal:2',
        'hutang_cicilan'    => 'decimal:2',
        'tagihan_listrik'   => 'decimal:2',
        'tagihan_air'       => 'decimal:2',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function subC1()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c1_id');
    }
    public function subC2()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c2_id');
    }
    public function subC3()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c3_id');
    }
    public function subC4()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c4_id');
    }
    public function subC5()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c5_id');
    }
    public function subC6()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c6_id');
    }
    public function subC7()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c7_id');
    }
    public function subC8()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c8_id');
    }
    public function subC9()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c9_id');
    }
    public function subC10()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c10_id');
    }
    public function subC11()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c11_id');
    }
    public function subC12()
    {
        return $this->belongsTo(SubKriteria::class, 'sub_c12_id');
    }
}
