<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_penilaians', function (Blueprint $table) {
            $table->id();
            $table->string('tipe_penilaian', 50);
            $table->string('nama_subjek');
            $table->decimal('persentase', 5, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_penilaians');
    }
};