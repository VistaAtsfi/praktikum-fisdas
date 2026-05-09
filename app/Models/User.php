<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'nim',
        'prodi',
        'jenis_kelamin',
        'nomor_ponsel',
        'google_id',
        'auth_provider',
        'profile_completed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'profile_completed' => 'boolean',
        ];
    }

    public function praktikans()
    {
        return $this->hasMany(Praktikan::class);
    }

    public function asistens()
    {
        return $this->hasMany(Asisten::class);
    }

    public function isPraktikan()
    {
        return $this->role === 'praktikan';
    }

    public function isAsisten()
    {
        return $this->role === 'asisten';
    }

    public function isKoordinator()
    {
        return $this->role === 'koordinator';
    }
}