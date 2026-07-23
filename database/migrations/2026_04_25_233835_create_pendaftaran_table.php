<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('periode_seleksi')->cascadeOnDelete();
            $table->foreignId('program_studi_id')->constrained('program_studi')->cascadeOnDelete();
            $table->string('no_pendaftaran')->unique();
            $table->enum('status', [
                'draft',
                'menunggu_verifikasi',
                'diverifikasi',
                'sedang_dihitung',
                'hasil_awal',
                'final_ukt',
                'ditolak_reviewer',
                'banding_diproses',
                'selesai'
            ])->default('draft');
            $table->string('nama_lengkap');
            $table->string('nik', 16)->unique();
            $table->string('no_hp')->unique();
            $table->string('asal_daerah');
            $table->string('provinsi');
            $table->timestamps();
            $table->unique(['user_id', 'periode_id']);
            $table->index('periode_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
