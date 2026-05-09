<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'jumlah_data',
        'field_pencarian',
        'kata_kunci_pencarian',
        'waktu_pemrosesan_server',
        'jumlah_iterasi',
        'jumlah_perbandingan',
        'penggunaan_memori',
        'waktu_pengujian',
        'keterangan_pengujian',
    ];

    protected $casts = [
        'waktu_pengujian' => 'datetime',
    ];
}