<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSorting extends Model
{
    use HasFactory;

    protected $fillable = [
        'jumlah_data',
        'field_pengurutan',
        'arah_pengurutan',
        'waktu_pemrosesan_server',
        'jumlah_perbandingan',
        'penggunaan_memori',
        'waktu_pengujian',
        'keterangan_pengujian',
    ];

    protected $casts = [
        'waktu_pengujian' => 'datetime',
    ];
}