<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_saw', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('periode_seleksi')->cascadeOnDelete();
            $table->decimal('skor_akhir', 8, 6);
            $table->integer('ranking')->nullable();
            $table->integer('rekomendasi_golongan');
            $table->timestamp('dihitung_pada');
            $table->timestamps();
            $table->unique('pendaftaran_id');
            $table->index(['periode_id', 'skor_akhir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_saw');
    }
};
