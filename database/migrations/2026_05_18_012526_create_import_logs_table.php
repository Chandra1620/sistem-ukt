<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('periode_nama')->nullable();
            $table->integer('total_baris')->default(0);
            $table->integer('berhasil')->default(0);
            $table->integer('gagal')->default(0);
            $table->integer('duplikat')->default(0);
            $table->enum('status', ['pending', 'processing', 'selesai', 'gagal'])->default('pending');
            $table->json('error_log')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
