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
    public function run(): void
    {
        $fisdas1 = Praktikum::firstOrCreate(
            [
                'jenis_praktikum' => 'fisdas_1',
                'tahun_akademik' => '2025/2026',
                'semester' => 'ganjil',
            ],
            [
                'nama' => 'Praktikum Fisika Dasar 1',
                'is_aktif' => true,
            ]
        );

        $fisdas2 = Praktikum::firstOrCreate(
            [
                'jenis_praktikum' => 'fisdas_2',
                'tahun_akademik' => '2025/2026',
                'semester' => 'genap',
            ],
            [
                'nama' => 'Praktikum Fisika Dasar 2',
                'is_aktif' => true,
            ]
        );

        $modulFisdas1 = [
            1 => 'Hukum Kekekalan Energi Mekanik',
            2 => 'Pengukuran Menggunakan Multimeter',
            3 => 'Simulasi Pencerminan',
            4 => 'Gerak Jatuh Bebas',
            5 => 'Resonansi dan Osilasi pada Pegas Helik',
            6 => 'Rangkaian Seri-Paralel',
            7 => 'Bayangan, Cermin, Warna dan Pembiasan',
            8 => 'Hambatan dalam Baterai dan Rangkaian Pembagi Tegangan',
        ];

        $modulFisdas2 = [
            1 => 'Modul Fisika Dasar 2 - 1',
            2 => 'Modul Fisika Dasar 2 - 2',
            3 => 'Modul Fisika Dasar 2 - 3',
            4 => 'Modul Fisika Dasar 2 - 4',
            5 => 'Modul Fisika Dasar 2 - 5',
            6 => 'Modul Fisika Dasar 2 - 6',
            7 => 'Modul Fisika Dasar 2 - 7',
            8 => 'Modul Fisika Dasar 2 - 8',
        ];

        foreach ($modulFisdas1 as $nomor => $namaModul) {
            Modul::updateOrCreate(
                [
                    'praktikum_id' => $fisdas1->id,
                    'nomor_modul' => $nomor,
                ],
                [
                    'nama_modul' => $namaModul,
                ]
            );
        }

        foreach ($modulFisdas2 as $nomor => $namaModul) {
            Modul::updateOrCreate(
                [
                    'praktikum_id' => $fisdas2->id,
                    'nomor_modul' => $nomor,
                ],
                [
                    'nama_modul' => $namaModul,
                ]
            );
        }

        User::updateOrCreate(
            [
                'email' => 'koordinator@fisdas.test',
            ],
            [
                'name' => 'Koordinator Praktikum',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'koordinator',
                'auth_provider' => 'email',
                'profile_completed' => true,
            ]
        );

        $asistenUser = User::updateOrCreate(
            [
                'email' => 'asisten@fisdas.test',
            ],
            [
                'name' => 'Asisten Praktikum',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'asisten',
                'nim' => '2209106000',
                'prodi' => 'Informatika',
                'jenis_kelamin' => 'P',
                'nomor_ponsel' => '081234567890',
                'auth_provider' => 'email',
                'profile_completed' => true,
            ]
        );

        Asisten::updateOrCreate(
            [
                'user_id' => $asistenUser->id,
                'praktikum_id' => $fisdas1->id,
            ],
            [
                'status' => 'aktif',
            ]
        );
    }
}