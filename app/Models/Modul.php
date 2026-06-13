<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'nomor_modul',
        'nama_modul',
        'urutan',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }
}