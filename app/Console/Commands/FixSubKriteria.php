<?php

namespace App\Console\Commands;

use App\Models\SubKriteria;
use Illuminate\Console\Command;

class FixSubKriteria extends Command
{
    protected $signature   = 'fix:sub-kriteria';
    protected $description = 'Fix range_min dan range_max sub kriteria';

    public function handle(): void
    {
        $subs = SubKriteria::all();
        foreach ($subs as $sub) {
            $min = $sub->range_min
                ? (float) str_replace(['.', ','], ['', '.'], $sub->range_min)
                : null;
            $max = $sub->range_max
                ? (float) str_replace(['.', ','], ['', '.'], $sub->range_max)
                : null;
            $sub->update(['range_min' => $min, 'range_max' => $max]);
            $this->info("Fixed: {$sub->label} -> min:{$min} max:{$max}");
        }
        $this->info('Selesai!');
    }
}
