<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            if (!Schema::hasColumn('kelompoks', 'prodi')) {
                $table->string('prodi')->nullable()->after('sesi');
            }
        });

        $indexes = DB::select("SHOW INDEX FROM kelompoks");

        $indexNames = collect($indexes)
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->toArray();

        if (in_array('unique_kelompok_praktikum_sesi', $indexNames)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->dropUnique('unique_kelompok_praktikum_sesi');
            });
        }

        $indexes = DB::select("SHOW INDEX FROM kelompoks");

        $indexNames = collect($indexes)
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->toArray();

        if (!in_array('unique_kelompok_praktikum_prodi_sesi', $indexNames)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->unique(
                    ['praktikum_id', 'prodi', 'nama_kelompok', 'sesi'],
                    'unique_kelompok_praktikum_prodi_sesi'
                );
            });
        }
    }

    public function down(): void
    {
        $indexes = DB::select("SHOW INDEX FROM kelompoks");

        $indexNames = collect($indexes)
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->toArray();

        if (in_array('unique_kelompok_praktikum_prodi_sesi', $indexNames)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->dropUnique('unique_kelompok_praktikum_prodi_sesi');
            });
        }

        $indexes = DB::select("SHOW INDEX FROM kelompoks");

        $indexNames = collect($indexes)
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->toArray();

        if (!in_array('unique_kelompok_praktikum_sesi', $indexNames)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->unique(
                    ['praktikum_id', 'nama_kelompok', 'sesi'],
                    'unique_kelompok_praktikum_sesi'
                );
            });
        }
    }
};