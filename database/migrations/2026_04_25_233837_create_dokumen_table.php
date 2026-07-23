<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel jenis_dokumen dulu
        Schema::create('jenis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('keterangan')->nullable();
            $table->boolean('wajib')->default(true);
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('jenis_dokumen_id')->nullable()->constrained('jenis_dokumen')->nullOnDelete();
            $table->string('jenis'); 
            $table->string('nama_file');
            $table->string('path');
            $table->string('mime_type');
            $table->integer('ukuran');
            $table->enum('status', ['pending', 'valid', 'invalid'])->default('pending');
            $table->timestamps();

            $table->index('pendaftaran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
        Schema::dropIfExists('jenis_dokumen');
    }
};
