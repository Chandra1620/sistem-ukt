<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_ekonomi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->decimal('penghasilan_ayah', 15, 2)->default(0);
            $table->decimal('penghasilan_ibu', 15, 2)->default(0);
            $table->decimal('penghasilan_total', 15, 2)->default(0);
            $table->unsignedBigInteger('sub_c1_id')->nullable();
            $table->unsignedBigInteger('sub_c2_id')->nullable();
            $table->unsignedBigInteger('sub_c3_id')->nullable();
            $table->unsignedBigInteger('sub_c4_id')->nullable();
            $table->unsignedBigInteger('sub_c5_id')->nullable();
            $table->unsignedBigInteger('sub_c6_id')->nullable();
            $table->unsignedBigInteger('sub_c7_id')->nullable();
            $table->unsignedBigInteger('sub_c8_id')->nullable();
            $table->unsignedBigInteger('sub_c9_id')->nullable();
            $table->unsignedBigInteger('sub_c10_id')->nullable();
            $table->unsignedBigInteger('sub_c11_id')->nullable();
            $table->unsignedBigInteger('sub_c12_id')->nullable();
            $table->integer('jumlah_tanggungan')->default(1);
            $table->string('kepemilikan_rumah')->default('menumpang');
            $table->decimal('luas_tanah', 10, 2)->nullable();
            $table->decimal('pbb', 15, 2)->default(0);
            $table->decimal('hutang_cicilan', 15, 2)->default(0);
            $table->decimal('tagihan_listrik', 15, 2)->default(0);
            $table->decimal('tagihan_air', 15, 2)->default(0);
            $table->string('kepemilikan_kendaraan')->default('tidak_ada');
            $table->string('pekerjaan_ayah')->default('tidak_bekerja');
            $table->string('pekerjaan_ibu')->default('tidak_bekerja');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_ekonomi');
    }
};
