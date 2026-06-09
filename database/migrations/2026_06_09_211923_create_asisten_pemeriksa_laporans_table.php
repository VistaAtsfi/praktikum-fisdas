<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asisten_pemeriksa_laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('praktikum_id')->constrained('praktikums')->cascadeOnDelete();
            $table->foreignId('kelompok_id')->constrained('kelompoks')->cascadeOnDelete();
            $table->foreignId('modul_id')->constrained('moduls')->cascadeOnDelete();
            $table->foreignId('asisten_id')->nullable()->constrained('asistens')->nullOnDelete();
            $table->string('prodi')->nullable();
            $table->timestamps();

            $table->unique(['praktikum_id', 'kelompok_id', 'modul_id'], 'unique_asisten_pemeriksa_modul');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asisten_pemeriksa_laporans');
    }
};