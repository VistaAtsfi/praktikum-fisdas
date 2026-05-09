<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asisten extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'praktikum_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }
}