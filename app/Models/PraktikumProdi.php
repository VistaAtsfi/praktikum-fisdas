<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PraktikumProdi extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'nama_prodi',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }
}