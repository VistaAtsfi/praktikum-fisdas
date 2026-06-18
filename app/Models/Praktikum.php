<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Praktikum extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis_praktikum',
        'tahun_akademik',
        'semester',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function kelompoks()
    {
        return $this->hasMany(Kelompok::class);
    }

    public function praktikans()
    {
        return $this->hasMany(Praktikan::class);
    }

    public function asistens()
    {
        return $this->hasMany(Asisten::class);
    }

    public function prodis()
    {
        return $this->hasMany(PraktikumProdi::class);
    }

    public function moduls()
    {
        return $this->hasMany(Modul::class);
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function jadwalLaporans()
    {
        return $this->hasMany(JadwalLaporan::class);
    }

    public function jadwalModulPraktikums()
    {
        return $this->hasMany(JadwalModulPraktikum::class);
    }

    public function nilaiAkhirs()
    {
        return $this->hasMany(NilaiAkhir::class);
    }
}