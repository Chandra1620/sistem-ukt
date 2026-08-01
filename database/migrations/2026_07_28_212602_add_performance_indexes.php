<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah indeks untuk mempercepat query yang sering dipakai.
 *
 * Catatan: PostgreSQL TIDAK otomatis membuat indeks pada kolom foreign key,
 * sehingga filter/groupBy/whereHas pada kolom di bawah ini sebelumnya
 * melakukan sequential scan. Indeks ini mempercepat halaman Management,
 * verifikasi, dan dashboard pada data ribuan baris.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            // Dipakai hampir di semua halaman Management (distribusi/statistik/proyeksi per prodi)
            $table->index('program_studi_id', 'pendaftaran_program_studi_id_index');
            // Banyak query menyaring status + periode bersamaan (dashboard & rekap)
            $table->index(['periode_id', 'status'], 'pendaftaran_periode_status_index');
        });

        Schema::table('data_ekonomi', function (Blueprint $table) {
            // Dipakai untuk eager-load & whereHas dari pendaftaran
            $table->index('pendaftaran_id', 'data_ekonomi_pendaftaran_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropIndex('pendaftaran_program_studi_id_index');
            $table->dropIndex('pendaftaran_periode_status_index');
        });

        Schema::table('data_ekonomi', function (Blueprint $table) {
            $table->dropIndex('data_ekonomi_pendaftaran_id_index');
        });
    }
};
