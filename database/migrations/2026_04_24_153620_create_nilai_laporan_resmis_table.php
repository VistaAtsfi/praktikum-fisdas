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
        Schema::create('nilai_laporan_resmis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->foreignId('praktikan_id')
                ->constrained('praktikans')
                ->cascadeOnDelete();

            // Contoh isi:
            // {
            //   "1": 50,
            //   "2": 50,
            //   "3": 50
            // }
            $table->json('nilai_modul')->nullable();

            $table->decimal('rata_rata', 6, 2)->nullable();

            $table->timestamps();

            $table->unique(['praktikum_id', 'praktikan_id'], 'unique_laporan_resmi_praktikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_laporan_resmis');
    }
};