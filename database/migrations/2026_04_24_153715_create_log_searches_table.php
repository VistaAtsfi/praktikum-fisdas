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
        Schema::create('log_searches', function (Blueprint $table) {
            $table->id();

            $table->integer('jumlah_data');
            $table->string('field_pencarian');
            $table->string('kata_kunci_pencarian');
            $table->double('waktu_pemrosesan_server');
            $table->integer('jumlah_iterasi')->default(0);
            $table->integer('jumlah_perbandingan')->default(0);
            $table->double('penggunaan_memori')->nullable();
            $table->timestamp('waktu_pengujian')->nullable();
            $table->text('keterangan_pengujian')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_searches');
    }
};