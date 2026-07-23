<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('akreditasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->decimal('nominal_kip', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Tambah foreign key di program_studi
        Schema::table('program_studi', function (Blueprint $table) {
            $table->foreignId('akreditasi_id')
                ->nullable()
                ->constrained('akreditasi')
                ->nullOnDelete()
                ->after('jenjang');
        });
    }

    public function down(): void
    {
        Schema::table('program_studi', function (Blueprint $table) {
            $table->dropForeign(['akreditasi_id']);
            $table->dropColumn('akreditasi_id');
        });

        Schema::dropIfExists('akreditasi');
    }
};
