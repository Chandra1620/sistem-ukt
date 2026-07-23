<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom akreditasi di program_studi
        Schema::table('program_studi', function (Blueprint $table) {
            $table->enum('akreditasi', ['Baik', 'Baik Sekali', 'Unggul'])
                ->default('Baik')
                ->after('jenjang');
        });

        // Tambah kolom status_kip di pendaftaran
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->boolean('is_kip')->default(false)->after('status');
            $table->string('no_kip', 50)->nullable()->after('is_kip');
        });

        // Tambah kolom sumber_pembiayaan di hasil_ukt
        Schema::table('hasil_ukt', function (Blueprint $table) {
            $table->enum('sumber_pembiayaan', ['Mandiri', 'KIP'])
                ->default('Mandiri')
                ->after('nominal_final');
            $table->string('dasar_penetapan')->nullable()->after('sumber_pembiayaan');
        });
    }

    public function down(): void
    {
        Schema::table('program_studi', function (Blueprint $table) {
            $table->dropColumn('akreditasi');
        });

        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropColumn(['is_kip', 'no_kip']);
        });

        Schema::table('hasil_ukt', function (Blueprint $table) {
            $table->dropColumn(['sumber_pembiayaan', 'dasar_penetapan']);
        });
    }
};
