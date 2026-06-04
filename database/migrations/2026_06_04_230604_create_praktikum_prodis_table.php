<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('praktikum_prodis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('praktikum_id')->constrained('praktikums')->cascadeOnDelete();
            $table->string('nama_prodi');
            $table->timestamps();

            $table->unique(['praktikum_id', 'nama_prodi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('praktikum_prodis');
    }
};