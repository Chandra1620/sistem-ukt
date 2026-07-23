<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode', 10);
            $table->decimal('bobot', 5, 2);
            $table->enum('tipe', ['cost', 'benefit']);
            $table->foreignId('periode_id')->constrained('periode_seleksi')->cascadeOnDelete();
            $table->timestamps();
            $table->integer('urutan')->default(0)->after('tipe');
            $table->index('periode_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};
