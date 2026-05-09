<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiKemampuanPraktikum extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'praktikan_id',
        'nilai_modul',
        'rata_rata_akhir',
    ];

    protected $casts = [
        'nilai_modul' => 'array',
        'rata_rata_akhir' => 'decimal:2',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function praktikan()
    {
        return $this->belongsTo(Praktikan::class);
    }
}