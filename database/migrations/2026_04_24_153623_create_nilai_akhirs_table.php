<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nilai_akhirs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->foreignId('praktikan_id')
                ->constrained('praktikans')
                ->cascadeOnDelete();

            $table->decimal('kehadiran', 6, 2)->nullable();
            $table->decimal('laporan', 6, 2)->nullable();
            $table->decimal('responsi', 6, 2)->nullable();
            $table->decimal('asistensi', 6, 2)->nullable();
            $table->decimal('kemampuan_praktik', 6, 2)->nullable();

            $table->decimal('nilai_angka', 6, 2)->nullable();
            $table->decimal('nilai_bobot', 3, 2)->nullable();
            $table->string('nilai_huruf')->nullable();

            $table->timestamps();

            $table->unique(['praktikum_id', 'praktikan_id'], 'unique_nilai_akhir_praktikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_akhirs');
    }
};