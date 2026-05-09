<?php

namespace Database\Seeders;

use App\Models\Asisten;
use App\Models\Kelompok;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\NilaiAkhir;
use App\Models\NilaiHarian;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PraktikanDemoSeeder extends Seeder
{
    public function run(): void
    {
        $praktikum = Praktikum::where('jenis_praktikum', 'fisdas_1')->first();

        if (!$praktikum) {
            $praktikum = Praktikum::create([
                'nama' => 'Praktikum Fisika Dasar 1',
                'jenis_praktikum' => 'fisdas_1',
                'tahun_akademik' => '2025/2026',
                'semester' => 'ganjil',
                'is_aktif' => true,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Kelompok Praktikum
        |--------------------------------------------------------------------------
        */
        $kelompok1 = Kelompok::firstOrCreate(
            [
                'praktikum_id' => $praktikum->id,
                'nama_kelompok' => 'Kelompok 1',
            ],
            [
                'sesi' => 'Sesi 1',
            ]
        );

        $kelompok2 = Kelompok::firstOrCreate(
            [
                'praktikum_id' => $praktikum->id,
                'nama_kelompok' => 'Kelompok 2',
            ],
            [
                'sesi' => 'Sesi 1',
            ]
        );

        $kelompok3 = Kelompok::firstOrCreate(
            [
                'praktikum_id' => $praktikum->id,
                'nama_kelompok' => 'Kelompok 3',
            ],
            [
                'sesi' => 'Sesi 2',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Modul Praktikum
        |--------------------------------------------------------------------------
        */
        $namaModul = [
            'Hukum Kekekalan Energi Mekanik',
            'Pengukuran Menggunakan Multimeter',
            'Simulasi Pencerminan',
            'Gerak Jatuh Bebas',
            'Resonansi dan Osilasi pada Pegas Helik',
            'Rangkaian Seri-Paralel',
            'Bayangan, Cermin, Warna dan Pembiasan',
            'Hambatan dalam Baterai dan Rangkaian Pembagi Tegangan',
        ];

        foreach ($namaModul as $nama) {
            Modul::firstOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'nama_modul' => $nama,
                ]
            );
        }

        $moduls = Modul::where('praktikum_id', $praktikum->id)
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Materi Praktikum
        |--------------------------------------------------------------------------
        | Link Google Drive ini dummy. Nanti tinggal diganti dari koordinator.
        */
        foreach ($moduls as $modul) {
            Materi::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'modul_id' => $modul->id,
                    'judul' => 'Materi ' . $modul->nama_modul,
                ],
                [
                    'deskripsi' => 'Materi praktikum untuk ' . $modul->nama_modul . '. File dapat dilihat dan diunduh melalui Google Drive.',
                    'link' => 'https://drive.google.com/file/d/ISI_FILE_ID_MODUL_' . $modul->id . '/preview',
                ]
            );
        }

        Materi::updateOrCreate(
            [
                'praktikum_id' => $praktikum->id,
                'modul_id' => null,
                'judul' => 'Panduan Umum Praktikum Fisika Dasar',
            ],
            [
                'deskripsi' => 'Panduan umum pelaksanaan praktikum, aturan laporan, dan ketentuan dasar praktikum.',
                'link' => 'https://drive.google.com/file/d/ISI_FILE_ID_PANDUAN_UMUM/preview',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Asisten Demo
        |--------------------------------------------------------------------------
        */
        $asistenUser = User::updateOrCreate(
            [
                'email' => 'asisten.demo@fisdas.test',
            ],
            [
                'name' => 'Hanna Amalia',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'asisten',
                'nim' => '2209106001',
                'prodi' => 'Fisika',
                'jenis_kelamin' => 'P',
                'nomor_ponsel' => '081234567891',
                'auth_provider' => 'email',
                'profile_completed' => true,
            ]
        );

        Asisten::updateOrCreate(
            [
                'user_id' => $asistenUser->id,
                'praktikum_id' => $praktikum->id,
            ],
            [
                'status' => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Praktikan Demo
        |--------------------------------------------------------------------------
        */
        $praktikanUser = User::updateOrCreate(
            [
                'email' => 'praktikan.demo@fisdas.test',
            ],
            [
                'name' => 'Mirza Syihabudin Almas',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'praktikan',
                'nim' => '2209076028',
                'prodi' => 'Teknik Industri',
                'jenis_kelamin' => 'L',
                'nomor_ponsel' => '081234567892',
                'auth_provider' => 'email',
                'profile_completed' => true,
            ]
        );

        $praktikan = Praktikan::firstOrCreate(
            [
                'user_id' => $praktikanUser->id,
                'praktikum_id' => $praktikum->id,
            ],
            [
                'kelompok_id' => $kelompok1->id,
                'qr_code' => 'QR-' . $praktikum->id . '-' . $praktikanUser->nim . '-' . strtoupper(Str::random(8)),
            ]
        );

        $praktikan->update([
            'kelompok_id' => $kelompok1->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Isi kelompok untuk praktikan existing yang belum punya kelompok
        |--------------------------------------------------------------------------
        */
        Praktikan::where('praktikum_id', $praktikum->id)
            ->whereNull('kelompok_id')
            ->update([
                'kelompok_id' => $kelompok1->id,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Nilai Harian Demo
        |--------------------------------------------------------------------------
        */
        NilaiHarian::updateOrCreate(
            [
                'praktikum_id' => $praktikum->id,
                'praktikan_id' => $praktikan->id,
            ],
            [
                'kehadiran' => [
                    '1' => '100',
                    '2' => '100',
                    '3' => '100',
                    '4' => '100',
                    '5' => '100',
                    '6' => '100',
                    '7' => '100',
                    '8' => '100',
                ],
                'rata_rata_kehadiran' => 100,
                'laporan' => 82.50,
                'responsi' => [
                    '1' => 80,
                    '2' => 78,
                    '3' => 85,
                    '4' => 82,
                    '5' => 80,
                    '6' => 84,
                    '7' => 88,
                    '8' => 86,
                ],
                'rata_rata_responsi' => 82.88,
                'asistensi' => [
                    '1' => 70,
                    '2' => 75,
                    '3' => 80,
                    '4' => 78,
                    '5' => 82,
                    '6' => 80,
                    '7' => 85,
                    '8' => 88,
                ],
                'rata_rata_asistensi' => 79.75,
                'kemampuan_praktik' => [
                    '1' => 75,
                    '2' => 80,
                    '3' => 78,
                    '4' => 82,
                    '5' => 85,
                    '6' => 80,
                    '7' => 86,
                    '8' => 88,
                ],
                'rata_rata_kemampuan_praktik' => 81.75,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Nilai Akhir Demo
        |--------------------------------------------------------------------------
        */
        NilaiAkhir::updateOrCreate(
            [
                'praktikum_id' => $praktikum->id,
                'praktikan_id' => $praktikan->id,
            ],
            [
                'kehadiran' => 100,
                'laporan' => 82.50,
                'responsi' => 82.88,
                'asistensi' => 79.75,
                'kemampuan_praktik' => 81.75,
                'nilai_angka' => 84.12,
                'nilai_bobot' => 3.75,
                'nilai_huruf' => 'A-',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Presensi Demo
        |--------------------------------------------------------------------------
        */
        foreach ($moduls as $modul) {
            Presensi::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                    'modul_id' => $modul->id,
                ],
                [
                    'asisten_id' => Asisten::where('user_id', $asistenUser->id)->first()?->id,
                    'waktu_absen' => now(),
                    'email' => $asistenUser->email,
                    'nim' => $praktikanUser->nim,
                    'nama' => $praktikanUser->name,
                    'kelompok' => $kelompok1->nama_kelompok,
                    'sesi' => $kelompok1->sesi,
                    'qr_code_scanned' => $praktikan->qr_code,
                    'status' => 'hadir',
                    'keterangan' => 'Presensi demo untuk ' . $modul->nama_modul,
                ]
            );
        }
    }
}