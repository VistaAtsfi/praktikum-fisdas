<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalModulPraktikum extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'prodi',
        'kelompok_id',
        'modul_id',
        'minggu_ke',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kelompok_label',
        'keterangan',
    ];

    protected $casts = [
        'minggu_ke' => 'integer',
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
}