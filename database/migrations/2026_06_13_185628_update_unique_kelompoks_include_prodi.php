<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            if (!Schema::hasColumn('kelompoks', 'prodi')) {
                $table->string('prodi')->nullable()->after('sesi');
            }
        });

        try {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->dropUnique('unique_kelompok_praktikum_sesi');
            });
        } catch (\Throwable $e) {
            // Abaikan kalau constraint lama tidak ada.
        }

        try {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->unique(
                    ['praktikum_id', 'prodi', 'nama_kelompok', 'sesi'],
                    'unique_kelompok_praktikum_prodi_sesi'
                );
            });
        } catch (\Throwable $e) {
            // Abaikan kalau constraint baru sudah ada.
        }
    }

    public function down(): void
    {
        try {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->dropUnique('unique_kelompok_praktikum_prodi_sesi');
            });
        } catch (\Throwable $e) {
            // Abaikan kalau constraint baru tidak ada.
        }

        try {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->unique(
                    ['praktikum_id', 'nama_kelompok', 'sesi'],
                    'unique_kelompok_praktikum_sesi'
                );
            });
        } catch (\Throwable $e) {
            // Abaikan kalau constraint lama sudah ada.
        }
    }
};