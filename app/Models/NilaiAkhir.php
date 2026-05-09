<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'praktikan_id',
        'kehadiran',
        'laporan',
        'responsi',
        'asistensi',
        'kemampuan_praktik',
        'nilai_angka',
        'nilai_bobot',
        'nilai_huruf',
    ];

    protected $casts = [
        'kehadiran' => 'decimal:2',
        'laporan' => 'decimal:2',
        'responsi' => 'decimal:2',
        'asistensi' => 'decimal:2',
        'kemampuan_praktik' => 'decimal:2',
        'nilai_angka' => 'decimal:2',
        'nilai_bobot' => 'decimal:2',
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