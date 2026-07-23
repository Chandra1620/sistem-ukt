<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE hasil_saw ALTER COLUMN skor_akhir TYPE numeric(10,6)');
        DB::statement('ALTER TABLE detail_saw ALTER COLUMN nilai_terbobot TYPE numeric(14,8)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE hasil_saw ALTER COLUMN skor_akhir TYPE numeric(8,6)');
        DB::statement('ALTER TABLE detail_saw ALTER COLUMN nilai_terbobot TYPE numeric(10,8)');
    }
};