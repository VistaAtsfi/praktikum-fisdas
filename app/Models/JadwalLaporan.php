<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'prodi',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }
}