<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiHarian extends Model
{
    use HasFactory;

    protected $fillable = [
        'praktikum_id',
        'praktikan_id',
        'kehadiran',
        'rata_rata_kehadiran',
        'laporan',
        'responsi',
        'rata_rata_responsi',
        'asistensi',
        'rata_rata_asistensi',
        'kemampuan_praktik',
        'rata_rata_kemampuan_praktik',
    ];

    protected $casts = [
        'kehadiran' => 'array',
        'responsi' => 'array',
        'asistensi' => 'array',
        'kemampuan_praktik' => 'array',
        'rata_rata_kehadiran' => 'decimal:2',
        'laporan' => 'decimal:2',
        'rata_rata_responsi' => 'decimal:2',
        'rata_rata_asistensi' => 'decimal:2',
        'rata_rata_kemampuan_praktik' => 'decimal:2',
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