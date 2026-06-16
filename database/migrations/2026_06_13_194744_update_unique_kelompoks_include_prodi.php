<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('kelompoks')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Kolom prodi harus tersedia
        |--------------------------------------------------------------------------
        */
        Schema::table('kelompoks', function (Blueprint $table) {
            if (!Schema::hasColumn('kelompoks', 'prodi')) {
                $table->string('prodi')->nullable()->after('sesi');
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Tambahkan index biasa untuk praktikum_id
        |--------------------------------------------------------------------------
        | MySQL membutuhkan index pada praktikum_id karena kolom ini dipakai foreign key
        | ke tabel praktikums. Index lama tidak boleh langsung dihapus sebelum ada
        | index pengganti.
        */
        $indexNames = $this->ambilNamaIndexKelompoks();

        if (!in_array('idx_kelompoks_praktikum_id_for_fk', $indexNames, true)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->index('praktikum_id', 'idx_kelompoks_praktikum_id_for_fk');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Tambahkan unique baru
        |--------------------------------------------------------------------------
        | Unique baru memasukkan prodi supaya:
        | - Teknik Elektro bisa punya Kelompok 1 Sesi 1
        | - Teknik Industri juga bisa punya Kelompok 1 Sesi 1
        | pada praktikum yang sama.
        */
        $indexNames = $this->ambilNamaIndexKelompoks();

        if (!in_array('unique_kelompok_praktikum_prodi_sesi', $indexNames, true)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->unique(
                    ['praktikum_id', 'prodi', 'nama_kelompok', 'sesi'],
                    'unique_kelompok_praktikum_prodi_sesi'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus unique lama
        |--------------------------------------------------------------------------
        | Setelah index pengganti tersedia, unique lama aman dihapus.
        */
        $indexNames = $this->ambilNamaIndexKelompoks();

        if (in_array('unique_kelompok_praktikum_sesi', $indexNames, true)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->dropUnique('unique_kelompok_praktikum_sesi');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('kelompoks')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Kembalikan unique lama
        |--------------------------------------------------------------------------
        | Supaya foreign key tetap punya index pendukung sebelum unique baru dihapus.
        */
        $indexNames = $this->ambilNamaIndexKelompoks();

        if (!in_array('unique_kelompok_praktikum_sesi', $indexNames, true)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->unique(
                    ['praktikum_id', 'nama_kelompok', 'sesi'],
                    'unique_kelompok_praktikum_sesi'
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus unique baru
        |--------------------------------------------------------------------------
        */
        $indexNames = $this->ambilNamaIndexKelompoks();

        if (in_array('unique_kelompok_praktikum_prodi_sesi', $indexNames, true)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->dropUnique('unique_kelompok_praktikum_prodi_sesi');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Hapus index tambahan jika ada
        |--------------------------------------------------------------------------
        */
        $indexNames = $this->ambilNamaIndexKelompoks();

        if (in_array('idx_kelompoks_praktikum_id_for_fk', $indexNames, true)) {
            Schema::table('kelompoks', function (Blueprint $table) {
                $table->dropIndex('idx_kelompoks_praktikum_id_for_fk');
            });
        }
    }

    private function ambilNamaIndexKelompoks(): array
    {
        if (!Schema::hasTable('kelompoks')) {
            return [];
        }

        $indexes = DB::select('SHOW INDEX FROM kelompoks');

        return collect($indexes)
            ->pluck('Key_name')
            ->unique()
            ->values()
            ->toArray();
    }
};