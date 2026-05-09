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
        Schema::create('nilai_kemampuan_praktikums', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->foreignId('praktikan_id')
                ->constrained('praktikans')
                ->cascadeOnDelete();

            // Contoh isi:
            // {
            //   "1": {
            //     "k1": 2,
            //     "k2": 3,
            //     "k3": 3,
            //     "k4": 1,
            //     "rerata": 57
            //   },
            //   "2": {
            //     "k1": 3,
            //     "k2": 3,
            //     "k3": 3,
            //     "k4": 2,
            //     "rerata": 75
            //   }
            // }
            $table->json('nilai_modul')->nullable();

            $table->decimal('rata_rata_akhir', 6, 2)->nullable();

            $table->timestamps();

            $table->unique(['praktikum_id', 'praktikan_id'], 'unique_kemampuan_praktikum_praktikan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_kemampuan_praktikums');
    }
};