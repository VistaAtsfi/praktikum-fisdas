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
        Schema::create('moduls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->string('nama_modul');
            $table->integer('urutan');

            $table->timestamps();

            $table->unique(['praktikum_id', 'urutan'], 'unique_modul_praktikum_urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moduls');
    }
};