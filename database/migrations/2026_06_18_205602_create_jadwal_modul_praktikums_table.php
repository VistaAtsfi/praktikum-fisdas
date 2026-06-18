<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_modul_praktikums', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->string('prodi', 100);
            $table->unsignedInteger('minggu_ke')->default(1);

            $table->foreignId('modul_id')
                ->nullable()
                ->constrained('moduls')
                ->nullOnDelete();

            $table->foreignId('kelompok_id')
                ->nullable()
                ->constrained('kelompoks')
                ->nullOnDelete();

            $table->string('kelompok_label')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('keterangan')->nullable();

            $table->timestamps();

            $table->index(['praktikum_id', 'prodi']);
            $table->index(['minggu_ke', 'jam_mulai', 'jam_selesai']);
            $table->index(['modul_id', 'kelompok_id']);

            $table->unique(
                ['praktikum_id', 'prodi', 'minggu_ke', 'modul_id', 'kelompok_id', 'jam_mulai', 'jam_selesai'],
                'unique_jadwal_modul_praktikum'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_modul_praktikums');
    }
};