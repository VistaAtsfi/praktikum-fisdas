<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moduls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->integer('nomor_modul');
            $table->string('nama_modul')->nullable();
            $table->text('deskripsi')->nullable();

            $table->timestamps();

            $table->unique(
                ['praktikum_id', 'nomor_modul'],
                'unique_modul_praktikum'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moduls');
    }
};