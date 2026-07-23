<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_saw', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_saw_id')->constrained('hasil_saw')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriteria')->cascadeOnDelete();
            $table->decimal('nilai_asli', 10, 4);
            $table->decimal('nilai_normalisasi', 10, 8);
            $table->decimal('nilai_terbobot', 10, 8);
            $table->timestamps();
            $table->index('hasil_saw_id');
            $table->index('kriteria_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_saw');
    }
};
