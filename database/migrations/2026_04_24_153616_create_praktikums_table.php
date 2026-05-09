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
        Schema::create('praktikums', function (Blueprint $table) {
            $table->id();

            $table->string('nama');
            $table->enum('jenis_praktikum', ['fisdas_1', 'fisdas_2']);
            $table->string('tahun_akademik');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->boolean('is_aktif')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('praktikums');
    }
};