<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_laporans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->string('prodi', 100);
            $table->string('hari', 30);
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('keterangan')->nullable();

            $table->timestamps();

            $table->index(['praktikum_id', 'prodi']);
            $table->index(['hari', 'jam_mulai', 'jam_selesai']);

            $table->unique(
                ['praktikum_id', 'prodi', 'hari', 'jam_mulai', 'jam_selesai'],
                'unique_jadwal_laporan'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_laporans');
    }
};