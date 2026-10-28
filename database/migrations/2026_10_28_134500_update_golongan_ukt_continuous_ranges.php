<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Merapikan data golongan UKT yang ada agar rentang skor menjadi kontinu tanpa celah 0.01:
     * - skor_max tiap golongan disamakan dengan skor_min golongan berikutnya.
     * - Golongan teratas (tertinggi) tetap memiliki skor_max = 100.00.
     */
    public function up(): void
    {
        $golongans = DB::table('golongan_ukt')
            ->orderBy('skor_min', 'asc')
            ->get();

        $count = $golongans->count();
        if ($count === 0) {
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            $current = $golongans[$i];

            if ($i < $count - 1) {
                // Untuk golongan 1 s.d. (N-1), skor_max = skor_min golongan berikutnya
                $next = $golongans[$i + 1];
                if ($next->skor_min !== null) {
                    DB::table('golongan_ukt')
                        ->where('id', $current->id)
                        ->update(['skor_max' => $next->skor_min]);
                }
            } else {
                // Golongan teratas: skor_max tetap 100.00
                DB::table('golongan_ukt')
                    ->where('id', $current->id)
                    ->update(['skor_max' => 100.00]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * Mengembalikan rentang batas diskrit dengan selisih celah 0.01 (misal 12.49, 24.99, dst).
     */
    public function down(): void
    {
        $golongans = DB::table('golongan_ukt')
            ->orderBy('skor_min', 'asc')
            ->get();

        $count = $golongans->count();
        if ($count === 0) {
            return;
        }

        for ($i = 0; $i < $count - 1; $i++) {
            $current = $golongans[$i];
            $next = $golongans[$i + 1];

            if ($next->skor_min !== null) {
                $revertedMax = round((float) $next->skor_min - 0.01, 2);
                DB::table('golongan_ukt')
                    ->where('id', $current->id)
                    ->update(['skor_max' => $revertedMax]);
            }
        }
    }
};
