<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'praktikan_id',
        'modul_id',
        'asisten_id',
        'waktu_absen',
        'email',
        'nim',
        'nama',
        'kelompok',
        'sesi',
        'qr_code_scanned',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'waktu_absen' => 'datetime',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function praktikan()
    {
        return $this->belongsTo(Praktikan::class);
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