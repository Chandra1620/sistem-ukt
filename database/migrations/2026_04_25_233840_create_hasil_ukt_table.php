<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_ukt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->integer('golongan_rekomendasi');
            $table->integer('golongan_final');
            $table->decimal('nominal_final', 12, 2);
            $table->boolean('is_override')->default(false);
            $table->text('alasan_override')->nullable();
            $table->foreignId('override_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('override_at')->nullable();
            $table->foreignId('finalisasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalisasi_at')->nullable();
            $table->timestamps();
            $table->unique('pendaftaran_id');
            $table->index('golongan_final');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_ukt');
    }
};
