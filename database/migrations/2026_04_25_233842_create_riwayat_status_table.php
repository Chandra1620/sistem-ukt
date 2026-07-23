<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->string('status_lama')->nullable();
            $table->string('status_baru');
            $table->string('keterangan')->nullable();
            $table->foreignId('diubah_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('pendaftaran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status');
    }
};
