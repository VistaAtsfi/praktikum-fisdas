<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenPemeriksaLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'kelompok_id',
        'modul_id',
        'asisten_id',
        'prodi',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }

    public function asisten()
    {
        return $this->belongsTo(Asisten::class);
    }
}