<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'prodi',
        'modul_id',
        'judul',
        'deskripsi',
        'link',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }
}