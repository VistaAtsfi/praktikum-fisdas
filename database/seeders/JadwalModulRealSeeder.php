<?php

namespace Database\Seeders;

use App\Models\JadwalModulPraktikum;
use App\Models\Kelompok;
use App\Models\Modul;
use App\Models\Praktikum;
use App\Models\PraktikumProdi;
use App\Models\Praktikan;
use Illuminate\Database\Seeder;

class JadwalModulRealSeeder extends Seeder
{
    private array $waktuSlots = [
        ['14:00', '14:40'],
        ['14:40', '15:20'],
        ['15:20', '16:00'],
        ['16:00', '16:40'],
    ];

    /*
    |--------------------------------------------------------------------------
    | Matriks sesuai file Pembagian Modul dan Sesi Fisdas
    |--------------------------------------------------------------------------
    | Format:
    | Minggu => [
    |   baris waktu 1 => [Modul 1, Modul 2, ..., Modul 8],
    |   baris waktu 2 => [Modul 1, Modul 2, ..., Modul 8],
    | ]
    |
    | Isi array adalah nomor kelompok.
    */
    private array $matriksJadwal = [
        1 => [
            [1, 2, 3, 4, 5, 6, 7, 8],
            [8, 1, 2, 3, 4, 5, 6, 7],
            [9, 10, 11, 12, 13, 14, 15, 16],
            [16, 9, 10, 11, 12, 13, 14, 15],
        ],
        2 => [
            [7, 8, 1, 2, 3, 4, 5, 6],
            [6, 7, 8, 1, 2, 3, 4, 5],
            [15, 16, 9, 10, 11, 12, 13, 14],
            [14, 15, 16, 9, 10, 11, 12, 13],
        ],
        3 => [
            [5, 6, 7, 8, 1, 2, 3, 4],
            [4, 5, 6, 7, 8, 1, 2, 3],
            [13, 14, 15, 16, 9, 10, 11, 12],
            [12, 13, 14, 15, 16, 9, 10, 11],
        ],
        4 => [
            [3, 4, 5, 6, 7, 8, 1, 2],
            [2, 3, 4, 5, 6, 7, 8, 1],
            [11, 12, 13, 14, 15, 16, 9, 10],
            [10, 11, 12, 13, 14, 15, 16, 9],
        ],
    ];

    public function run(): void
    {
        $this->command?->info('Memperbaiki jadwal modul praktikum sesuai matriks data real...');

        $praktikums = Praktikum::query()
            ->where('tahun_akademik', '2025/2026')
            ->where(function ($query) {
                $query->where('jenis_praktikum', 'fisdas_1')
                    ->orWhere('jenis_praktikum', 'fisdas_2')
                    ->orWhere('nama', 'like', '%Fisika Dasar%');
            })
            ->orderBy('id')
            ->get();

        if ($praktikums->isEmpty()) {
            $this->command?->warn('Tidak ada periode praktikum Fisdas 2025/2026.');
            return;
        }

        foreach ($praktikums as $praktikum) {
            $this->seedUntukPraktikum($praktikum);
        }

        $this->command?->info('Jadwal modul praktikum berhasil diperbaiki.');
    }

    private function seedUntukPraktikum(Praktikum $praktikum): void
    {
        $prodis = $this->ambilProdiPraktikum($praktikum);

        if ($prodis->isEmpty()) {
            $this->command?->warn("Tidak ada prodi untuk {$praktikum->nama}.");
            return;
        }

        $moduls = Modul::where('praktikum_id', $praktikum->id)
            ->orderBy('nomor_modul')
            ->orderBy('id')
            ->get()
            ->keyBy('nomor_modul');

        if ($moduls->count() < 8) {
            $this->command?->warn("Modul untuk {$praktikum->nama} belum lengkap 8 modul.");
            return;
        }

        foreach ($prodis as $prodi) {
            /*
            |--------------------------------------------------------------------------
            | Hapus jadwal modul lama untuk prodi ini
            |--------------------------------------------------------------------------
            | Ini penting supaya data salah dari seeder lama tidak ikut tampil.
            */
            JadwalModulPraktikum::where('praktikum_id', $praktikum->id)
                ->where('prodi', $prodi)
                ->delete();

            $this->pastikanKelompokLengkap($praktikum, $prodi);

            foreach ($this->matriksJadwal as $mingguKe => $barisMinggu) {
                foreach ($barisMinggu as $indexWaktu => $kelompokPerModul) {
                    $jamMulai = $this->waktuSlots[$indexWaktu][0];
                    $jamSelesai = $this->waktuSlots[$indexWaktu][1];

                    foreach ($kelompokPerModul as $indexModul => $nomorKelompok) {
                        $nomorModul = $indexModul + 1;

                        $modul = $moduls[$nomorModul] ?? null;

                        if (!$modul) {
                            continue;
                        }

                        $kelompok = $this->getOrCreateKelompok(
                            praktikum: $praktikum,
                            prodi: $prodi,
                            nomorKelompok: $nomorKelompok
                        );

                        JadwalModulPraktikum::create([
                            'praktikum_id' => $praktikum->id,
                            'prodi' => $prodi,
                            'kelompok_id' => $kelompok->id,
                            'modul_id' => $modul->id,
                            'minggu_ke' => $mingguKe,

                            /*
                            |--------------------------------------------------------------------------
                            | File pembagian modul yang kamu kirim tidak menampilkan kolom hari.
                            |--------------------------------------------------------------------------
                            | Jadi sementara diisi Senin agar kolom Hari tidak kosong.
                            | Nanti koordinator tetap bisa edit dari halaman jadwal.
                            */
                            'hari' => 'Senin',

                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                            'kelompok_label' => $kelompok->nama_kelompok,
                            'keterangan' => null,
                        ]);
                    }
                }
            }

            $this->command?->info("Jadwal {$praktikum->nama} - {$prodi} selesai.");
        }
    }

    private function ambilProdiPraktikum(Praktikum $praktikum)
    {
        $prodisDariPeriode = PraktikumProdi::where('praktikum_id', $praktikum->id)
            ->pluck('nama_prodi');

        $prodisDariKelompok = Kelompok::where('praktikum_id', $praktikum->id)
            ->pluck('prodi');

        $prodisDariPraktikan = Praktikan::with('user')
            ->where('praktikum_id', $praktikum->id)
            ->get()
            ->pluck('user.prodi');

        return $prodisDariPeriode
            ->merge($prodisDariKelompok)
            ->merge($prodisDariPraktikan)
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    private function pastikanKelompokLengkap(Praktikum $praktikum, string $prodi): void
    {
        for ($nomorKelompok = 1; $nomorKelompok <= 16; $nomorKelompok++) {
            $this->getOrCreateKelompok(
                praktikum: $praktikum,
                prodi: $prodi,
                nomorKelompok: $nomorKelompok
            );
        }
    }

    private function getOrCreateKelompok(
        Praktikum $praktikum,
        string $prodi,
        int $nomorKelompok
    ): Kelompok {
        $nomorSesi = $nomorKelompok <= 8 ? 1 : 2;

        return Kelompok::updateOrCreate(
            [
                'praktikum_id' => $praktikum->id,
                'prodi' => $prodi,
                'nama_kelompok' => 'Kelompok ' . $nomorKelompok,
                'sesi' => 'Sesi ' . $nomorSesi,
            ],
            []
        );
    }
}