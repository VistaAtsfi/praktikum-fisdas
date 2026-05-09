<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Praktikan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'praktikum_id',
        'kelompok_id',
        'qr_code',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat_asal',
        'alamat_sekarang',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function nilaiLaporanResmi()
    {
        return $this->hasOne(NilaiLaporanResmi::class);
    }

    public function nilaiPenulisanLaporan()
    {
        return $this->hasOne(NilaiPenulisanLaporan::class);
    }

    public function nilaiKemampuanPraktikum()
    {
        return $this->hasOne(NilaiKemampuanPraktikum::class);
    }

    public function nilaiHarian()
    {
        return $this->hasOne(NilaiHarian::class);
    }

    public function nilaiAkhir()
    {
        return $this->hasOne(NilaiAkhir::class);
    }
}