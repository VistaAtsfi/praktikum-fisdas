<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPenilaian extends Model
{
    protected $fillable = [
        'tipe_penilaian',
        'nama_subjek',
        'persentase',
        'keterangan',
        'urutan',
        'is_aktif',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'urutan' => 'integer',
        'is_aktif' => 'boolean',
    ];
}