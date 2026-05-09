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
        Schema::create('nilai_penulisan_laporans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->foreignId('praktikan_id')
                ->constrained('praktikans')
                ->cascadeOnDelete();

            // Contoh isi:
            // {
            //   "1": 30,
            //   "2": 65,
            //   "3": 30
            // }
            $table->json('laporan_mingguan')->nullable();

            $table->decimal('rata_rata_mingguan', 6, 2)->nullable();
            $table->decimal('rata_rata_resmi', 6, 2)->nullable();
            $table->decimal('nilai_akhir', 6, 2)->nullable();

            $table->timestamps();

            $table->unique(['praktikum_id', 'praktikan_id'], 'unique_penulisan_laporan_praktikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_penulisan_laporans');
    }
};