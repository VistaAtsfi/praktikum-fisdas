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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('praktikum_id')
                ->constrained('praktikums')
                ->cascadeOnDelete();

            $table->foreignId('praktikan_id')
                ->nullable()
                ->constrained('praktikans')
                ->nullOnDelete();

            $table->foreignId('modul_id')
                ->nullable()
                ->constrained('moduls')
                ->nullOnDelete();

            // Asisten yang melakukan scan QR.
            $table->foreignId('asisten_id')
                ->nullable()
                ->constrained('asistens')
                ->nullOnDelete();

            // Waktu saat QR discan / absen tercatat.
            $table->timestamp('waktu_absen')->nullable();

            $table->string('email')->nullable();
            $table->string('nim');
            $table->string('nama');
            $table->string('kelompok')->nullable();
            $table->string('sesi')->nullable();

            // QR yang discan saat presensi.
            $table->string('qr_code_scanned')->nullable();

            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Hadir');
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index('nim');
            $table->index('nama');
            $table->index('status');
            $table->index('qr_code_scanned');

            $table->unique(
                ['praktikum_id', 'praktikan_id', 'modul_id', 'sesi'],
                'unique_presensi_praktikan_modul_sesi'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};