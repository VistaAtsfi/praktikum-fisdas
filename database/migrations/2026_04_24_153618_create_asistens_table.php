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
        Schema::create('asistens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('praktikum_id')
                ->nullable()
                ->constrained('praktikums')
                ->nullOnDelete();

            $table->enum('status', ['aktif', 'tidak aktif'])->default('aktif');

            $table->timestamps();

            $table->unique(['user_id', 'praktikum_id'], 'unique_asisten_praktikum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistens');
    }
};