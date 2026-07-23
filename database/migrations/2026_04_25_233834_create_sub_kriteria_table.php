<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_kriteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_id')->constrained('kriteria')->cascadeOnDelete();
            $table->string('label');
            $table->decimal('nilai', 5, 2);
            $table->string('range_min')->nullable();
            $table->string('range_max')->nullable();
            $table->timestamps();

            $table->index('kriteria_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_kriteria');
    }
};
