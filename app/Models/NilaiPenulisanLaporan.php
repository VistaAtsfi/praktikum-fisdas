<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPenulisanLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'praktikan_id',
        'laporan_mingguan',
        'rata_rata_mingguan',
        'rata_rata_resmi',
        'nilai_akhir',
    ];

    protected $casts = [
        'laporan_mingguan' => 'array',
        'rata_rata_mingguan' => 'decimal:2',
        'rata_rata_resmi' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
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