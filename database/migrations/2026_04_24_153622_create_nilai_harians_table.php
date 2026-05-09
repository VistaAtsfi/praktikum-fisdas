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
        Schema::create('nilai_harians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->foreignId('praktikan_id')
                ->constrained('praktikans')
                ->cascadeOnDelete();

            // Contoh isi:
            // {
            //   "1": 100,
            //   "2": 100,
            //   "3": 100
            // }
            $table->json('kehadiran')->nullable();
            $table->decimal('rata_rata_kehadiran', 6, 2)->nullable();

            $table->decimal('laporan', 6, 2)->nullable();

            // Contoh isi:
            // {
            //   "1": 70,
            //   "2": 70,
            //   "3": 70
            // }
            $table->json('responsi')->nullable();
            $table->decimal('rata_rata_responsi', 6, 2)->nullable();

            // Contoh isi:
            // {
            //   "1": 30,
            //   "2": 30,
            //   "3": 30
            // }
            $table->json('asistensi')->nullable();
            $table->decimal('rata_rata_asistensi', 6, 2)->nullable();

            // Contoh isi:
            // {
            //   "1": 57,
            //   "2": 75,
            //   "3": 60
            // }
            $table->json('kemampuan_praktik')->nullable();
            $table->decimal('rata_rata_kemampuan_praktik', 6, 2)->nullable();

            $table->timestamps();

            $table->unique(['praktikum_id', 'praktikan_id'], 'unique_nilai_harian_praktikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_harians');
    }
};