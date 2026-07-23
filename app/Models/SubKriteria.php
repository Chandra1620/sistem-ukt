<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    protected $table = 'sub_kriteria';

    protected $fillable = [
        'kriteria_id',
        'label',
        'nilai',
        'range_min',
        'range_max',
    ];

    // Fix route model binding
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
