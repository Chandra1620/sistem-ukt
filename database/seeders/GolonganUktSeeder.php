<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GolonganUkt;

class GolonganUktSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['golongan' => 1, 'nominal' => 500000,   'skor_min' => 0.00, 'skor_max' => 12.49],
            ['golongan' => 2, 'nominal' => 1000000,  'skor_min' => 12.50, 'skor_max' => 24.99],
            ['golongan' => 3, 'nominal' => 2500000,  'skor_min' => 25.00, 'skor_max' => 37.49],
            ['golongan' => 4, 'nominal' => 4500000,  'skor_min' => 37.50, 'skor_max' => 49.99],
            ['golongan' => 5, 'nominal' => 5000000,  'skor_min' => 50.00, 'skor_max' => 62.49],
            ['golongan' => 6, 'nominal' => 5500000,  'skor_min' => 62.50, 'skor_max' => 74.99],
            ['golongan' => 7, 'nominal' => 6000000,  'skor_min' => 75.00, 'skor_max' => 87.49],
            ['golongan' => 8, 'nominal' => 7000000,  'skor_min' => 87.50, 'skor_max' => 100.00],
        ];

        foreach ($data as $item) {
            GolonganUkt::firstOrCreate(
                ['golongan' => $item['golongan']],
                [
                    'nominal'  => $item['nominal'],
                    'skor_min' => $item['skor_min'],
                    'skor_max' => $item['skor_max'],
                ]
            );
        }
    }
}
