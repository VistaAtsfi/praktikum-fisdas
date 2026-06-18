<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_modul_praktikums', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_modul_praktikums', 'hari')) {
                $table->string('hari')->nullable()->after('minggu_ke');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_modul_praktikums', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_modul_praktikums', 'hari')) {
                $table->dropColumn('hari');
            }
        });
    }
};