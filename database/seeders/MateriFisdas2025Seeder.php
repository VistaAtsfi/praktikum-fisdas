<?php

namespace Database\Seeders;

use App\Models\Materi;
use App\Models\Modul;
use App\Models\Praktikum;
use Illuminate\Database\Seeder;

class MateriFisdas2025Seeder extends Seeder
{
    public function run(): void
    {
        $linksByProdi = [
            'Teknik Elektro' => 'https://drive.google.com/file/d/1Fa2zuj7BNmfDiH8JIx-GytNQSu7BIlrt/view',

            'Teknik Geologi dan Pertambangan' => 'https://drive.google.com/file/d/1zVTPOK_jPcxO0i0zyekmkNRAWvhsK-ff/view',

            'Teknik Industri' => 'https://drive.google.com/file/d/1LkjuyrqtiYqMjsdEOvkmMXk0LHHyXnqB/view',

            'Teknik Lingkungan' => 'https://drive.google.com/file/d/1vUNI8HNKUMmrUfUt0cxTSNdiFIEAymSd/view',
        ];

        $praktikums = Praktikum::query()
            ->whereIn('jenis_praktikum', ['fisdas_1', 'fisdas_2'])
            ->get();

        if ($praktikums->isEmpty()) {
            $this->command?->warn('Tidak ada data praktikum Fisdas.');
            return;
        }

        foreach ($praktikums as $praktikum) {
            /*
            |--------------------------------------------------------------------------
            | Hapus semua materi lama pada praktikum ini
            |--------------------------------------------------------------------------
            | Dibuat 4 materi, masing-masing 1 prodi.
            */
            Materi::where('praktikum_id', $praktikum->id)->delete();

            /*
            |--------------------------------------------------------------------------
            | Pakai Modul 1 saja
            |--------------------------------------------------------------------------
            | Semua prodi memakai nomor_modul 1, karena file modulnya masih gabungan.
            */
            $modul = Modul::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'nomor_modul' => 1,
                ],
                [
                    'nama_modul' => 'Modul 1',
                ]
            );

            foreach ($linksByProdi as $prodi => $link) {
                Materi::create([
                    'praktikum_id' => $praktikum->id,
                    'prodi' => $prodi,
                    'modul_id' => $modul->id,
                    'judul' => 'Modul Fisdas 2025 ' . $prodi,
                    'deskripsi' => 'File modul praktikum Fisika Dasar 2025 untuk ' . $prodi . '. Untuk sementara, seluruh materi praktikum ditampilkan dalam satu file modul gabungan.',
                    'link' => $link,
                ]);
            }
        }

        $this->command?->info('Seeder materi selesai. Total materi sekarang: ' . Materi::count());
    }
}