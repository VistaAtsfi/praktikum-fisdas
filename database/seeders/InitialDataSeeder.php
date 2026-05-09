<?php

namespace Database\Seeders;

use App\Models\Asisten;
use App\Models\Modul;
use App\Models\Praktikum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fisdas1 = Praktikum::create([
            'nama' => 'Praktikum Fisika Dasar 1',
            'jenis_praktikum' => 'fisdas_1',
            'tahun_akademik' => '2025/2026',
            'semester' => 'ganjil',
            'is_aktif' => true,
        ]);

        $fisdas2 = Praktikum::create([
            'nama' => 'Praktikum Fisika Dasar 2',
            'jenis_praktikum' => 'fisdas_2',
            'tahun_akademik' => '2025/2026',
            'semester' => 'genap',
            'is_aktif' => true,
        ]);

        for ($i = 1; $i <= 8; $i++) {
            Modul::create([
                'praktikum_id' => $fisdas1->id,
                'nama_modul' => 'Modul ' . $i,
                'urutan' => $i,
            ]);
        }

        for ($i = 1; $i <= 8; $i++) {
            Modul::create([
                'praktikum_id' => $fisdas2->id,
                'nama_modul' => 'Modul ' . $i,
                'urutan' => $i,
            ]);
        }

        User::create([
            'name' => 'Koordinator Praktikum',
            'email' => 'koordinator@fisdas.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'koordinator',
            'auth_provider' => 'email',
            'profile_completed' => true,
        ]);

        $asistenUser = User::create([
            'name' => 'Asisten Praktikum',
            'email' => 'asisten@fisdas.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'asisten',
            'nim' => '2209106000',
            'prodi' => 'Informatika',
            'jenis_kelamin' => 'P',
            'nomor_ponsel' => '081234567890',
            'auth_provider' => 'email',
            'profile_completed' => true,
        ]);

        Asisten::create([
            'user_id' => $asistenUser->id,
            'praktikum_id' => $fisdas1->id,
            'status' => 'aktif',
        ]);
    }
}