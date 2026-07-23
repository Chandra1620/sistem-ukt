<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banding', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('hasil_ukt_id')->constrained('hasil_ukt')->cascadeOnDelete();
            $table->text('alasan');
            $table->enum('status', [
                'diajukan',
                'diproses',
                'diterima',
                'ditolak'
            ])->default('diajukan');
            $table->text('keputusan_panitia')->nullable();
            $table->integer('golongan_baru')->nullable();
            $table->decimal('nominal_baru', 12, 2)->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamps();

            $table->index('pendaftaran_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banding');
    }
};
