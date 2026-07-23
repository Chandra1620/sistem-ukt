<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramStudiSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama dulu
        DB::table('program_studi')->truncate();

        $data = [
            // D3
            ['nama' => 'D3 Teknik Mesin',                              'jenjang' => 'D3'],
            ['nama' => 'D3 Teknik Pendingin dan Tata Udara',           'jenjang' => 'D3'],
            ['nama' => 'D3 Teknik Informatika',                        'jenjang' => 'D3'],
            ['nama' => 'D3 Keperawatan',                               'jenjang' => 'D3'],

            // D4
            ['nama' => 'D4 Perancangan Manufaktur',                    'jenjang' => 'D4'],
            ['nama' => 'D4 Rekayasa Perangkat Lunak',                  'jenjang' => 'D4'],
            ['nama' => 'D4 Sistem Informasi Kota Cerdas',              'jenjang' => 'D4'],
            ['nama' => 'D4 Rekayasa Instrumental dan Kontrol',         'jenjang' => 'D4'],
            ['nama' => 'D4 Teknologi Rekayasa Elektro Medis',          'jenjang' => 'D4'],
            ['nama' => 'D4 Teknologi Laboratorium Medis',              'jenjang' => 'D4'],
            ['nama' => 'D4 Teknologi Material',                        'jenjang' => 'D4'],
            ['nama' => 'D4 Teknologi Rekayasa Komputer',               'jenjang' => 'D4'],
        ];

        foreach ($data as $item) {
            DB::statement("
                INSERT INTO program_studi (nama, jenjang, aktif, created_at, updated_at)
                VALUES (?, ?, TRUE, NOW(), NOW())
            ", [$item['nama'], $item['jenjang']]);
        }
    }
}
