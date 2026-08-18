<?php

namespace Database\Seeders;

use App\Models\JadwalLaporan;
use App\Models\JadwalModulPraktikum;
use App\Models\Kelompok;
use App\Models\Modul;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\PraktikumProdi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FisdasJadwalKelompokRealDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('Mulai import jadwal dan kelompok Fisdas Ganjil 2025...');

        $praktikum = Praktikum::updateOrCreate(
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

        $this->pastikanModulFisdas1($praktikum);

        $this->importKelompokPraktikum(
            $praktikum,
            'Kelompok Praktikum Fisdas Ganjil 2025.xlsx'
        );

        $this->importJadwalLaporan(
            $praktikum,
            'Jadwal Pengumpulan Laporan Ganjil 2025.xlsx'
        );

        $this->importJadwalModulSesi(
            $praktikum,
            'Pembagian Modul dan Sesi Fisdas 2025 Ganjil.xlsx'
        );

        $this->command?->info('Import jadwal dan kelompok selesai.');
        $this->command?->info('Total kelompok: ' . Kelompok::where('praktikum_id', $praktikum->id)->count());
        $this->command?->info('Total praktikan: ' . Praktikan::where('praktikum_id', $praktikum->id)->count());
        $this->command?->info('Total jadwal laporan: ' . JadwalLaporan::where('praktikum_id', $praktikum->id)->count());
        $this->command?->info('Total jadwal modul: ' . JadwalModulPraktikum::where('praktikum_id', $praktikum->id)->count());
    }

    private function pastikanModulFisdas1(Praktikum $praktikum): void
    {
        $moduls = [
            1 => 'Hukum Kekekalan Energi Mekanik',
            2 => 'Pengukuran Menggunakan Multimeter',
            3 => 'Simulasi Pencerminan',
            4 => 'Gerak Jatuh Bebas',
            5 => 'Resonansi dan Osilasi pada Pegas Helik',
            6 => 'Rangkaian Seri-Paralel',
            7 => 'Bayangan, Cermin, Warna dan Pembiasan',
            8 => 'Hambatan dalam Baterai dan Rangkaian Pembagi Tegangan',
        ];

        foreach ($moduls as $nomor => $nama) {
            Modul::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'nomor_modul' => $nomor,
                ],
                [
                    'nama_modul' => $nama,
                ]
            );
        }
    }

    private function importKelompokPraktikum(Praktikum $praktikum, string $filename): void
    {
        $path = $this->dataPath($filename);

        if (!file_exists($path)) {
            $this->command?->warn("File kelompok tidak ditemukan: {$filename}");
            return;
        }

        $spreadsheet = IOFactory::load($path);

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $prodi = $this->prodiDariNamaSheet($sheet->getTitle());

            if (!$prodi) {
                continue;
            }

            PraktikumProdi::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'nama_prodi' => $prodi,
                ],
                []
            );

            $rowTerakhir = $sheet->getHighestDataRow();

            $kelompokNomorSekarang = null;
            $sesiNomorSekarang = null;

            for ($row = 6; $row <= $rowTerakhir; $row++) {
                $nim = $this->bersihkanNim($this->cellValue($sheet, 2, $row));
                $nama = $this->bersihkanTeks($this->cellValue($sheet, 3, $row));
                $kelompokRaw = $this->cellValue($sheet, 4, $row);
                $sesiRaw = $this->cellValue($sheet, 5, $row);

                if (!$this->nimValid($nim) || $nama === '') {
                    continue;
                }

                $kelompokNomorBaru = $this->nomorDariTeks($kelompokRaw);
                $sesiNomorBaru = $this->nomorDariTeks($sesiRaw);

                if ($kelompokNomorBaru) {
                    $kelompokNomorSekarang = $kelompokNomorBaru;
                }

                if ($sesiNomorBaru) {
                    $sesiNomorSekarang = $sesiNomorBaru;
                }

                if (!$kelompokNomorSekarang) {
                    $kelompokNomorSekarang = 1;
                }

                if (!$sesiNomorSekarang) {
                    $sesiNomorSekarang = $this->sesiDariNomorKelompok($kelompokNomorSekarang);
                }

                $kelompok = $this->getOrCreateKelompok(
                    praktikum: $praktikum,
                    prodi: $prodi,
                    nomorKelompok: $kelompokNomorSekarang,
                    nomorSesi: $sesiNomorSekarang
                );

                $this->getOrCreatePraktikan(
                    praktikum: $praktikum,
                    prodi: $prodi,
                    nim: $nim,
                    nama: $nama,
                    kelompok: $kelompok
                );
            }

            $this->command?->info("Import kelompok selesai: {$prodi}");
        }
    }

    private function importJadwalLaporan(Praktikum $praktikum, string $filename): void
    {
        $path = $this->dataPath($filename);

        if (!file_exists($path)) {
            $this->command?->warn("File jadwal laporan tidak ditemukan: {$filename}");
            return;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);

        $rowTerakhir = $sheet->getHighestDataRow();
        $colTerakhir = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        for ($row = 6; $row <= $rowTerakhir; $row++) {
            $jamRaw = $this->bersihkanTeks($this->cellValue($sheet, 1, $row));

            if (!$this->adaRentangJam($jamRaw)) {
                continue;
            }

            [$jamMulai, $jamSelesai] = $this->pecahRentangJam($jamRaw);

            for ($col = 2; $col <= $colTerakhir; $col++) {
                $hari = $this->bersihkanTeks($this->cellValue($sheet, $col, 5));
                $isi = $this->bersihkanTeks($this->cellValue($sheet, $col, $row));

                if ($hari === '' || $isi === '') {
                    continue;
                }

                $items = preg_split('/,|;|\n/', $isi);

                foreach ($items as $item) {
                    $item = $this->bersihkanTeks($item);

                    if ($item === '') {
                        continue;
                    }

                    [$prodi, $keterangan] = $this->prodiDariLabelJadwalLaporan($item);

                    if (!$prodi) {
                        continue;
                    }

                    PraktikumProdi::updateOrCreate(
                        [
                            'praktikum_id' => $praktikum->id,
                            'nama_prodi' => $prodi,
                        ],
                        []
                    );

                    JadwalLaporan::updateOrCreate(
                        [
                            'praktikum_id' => $praktikum->id,
                            'prodi' => $prodi,
                            'hari' => $hari,
                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                        ],
                        [
                            'keterangan' => $keterangan,
                        ]
                    );
                }
            }
        }

        $this->command?->info("Import jadwal laporan selesai: {$filename}");
    }

    private function importJadwalModulSesi(Praktikum $praktikum, string $filename): void
    {
        $path = $this->dataPath($filename);

        if (!file_exists($path)) {
            $this->command?->warn("File pembagian modul dan sesi tidak ditemukan: {$filename}");
            return;
        }

        $spreadsheet = IOFactory::load($path);

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $prodi = $this->prodiDariNamaSheet($sheet->getTitle());

            if (!$prodi) {
                continue;
            }

            PraktikumProdi::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'nama_prodi' => $prodi,
                ],
                []
            );

            $rowTerakhir = $sheet->getHighestDataRow();
            $colTerakhir = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

            $mingguKe = null;

            for ($row = 6; $row <= $rowTerakhir; $row++) {
                $kolomWaktu = $this->bersihkanTeks($this->cellValue($sheet, 1, $row));

                if ($this->isMingguLabel($kolomWaktu)) {
                    $mingguKe = $this->nomorMinggu($kolomWaktu);
                    continue;
                }

                if (!$mingguKe || !$this->adaRentangJam($kolomWaktu)) {
                    continue;
                }

                [$jamMulai, $jamSelesai] = $this->pecahRentangJam($kolomWaktu);

                for ($col = 2; $col <= min($colTerakhir, 9); $col++) {
                    $headerModul = $this->bersihkanTeks($this->cellValue($sheet, $col, 5));
                    $kelompokLabel = $this->bersihkanTeks($this->cellValue($sheet, $col, $row));

                    if ($headerModul === '' || $kelompokLabel === '') {
                        continue;
                    }

                    if (strtolower($kelompokLabel) === 'blank') {
                        continue;
                    }

                    $nomorModul = $this->nomorDariTeks($headerModul);
                    $nomorKelompok = $this->nomorDariTeks($kelompokLabel);

                    if (!$nomorModul || !$nomorKelompok) {
                        continue;
                    }

                    $modul = Modul::where('praktikum_id', $praktikum->id)
                        ->where('nomor_modul', $nomorModul)
                        ->first();

                    if (!$modul) {
                        continue;
                    }

                    $kelompok = Kelompok::where('praktikum_id', $praktikum->id)
                        ->where('prodi', $prodi)
                        ->where('nama_kelompok', 'Kelompok ' . $nomorKelompok)
                        ->first();

                    if (!$kelompok) {
                        $kelompok = $this->getOrCreateKelompok(
                            praktikum: $praktikum,
                            prodi: $prodi,
                            nomorKelompok: $nomorKelompok,
                            nomorSesi: $this->sesiDariNomorKelompok($nomorKelompok)
                        );
                    }

                    JadwalModulPraktikum::updateOrCreate(
                        [
                            'praktikum_id' => $praktikum->id,
                            'prodi' => $prodi,
                            'minggu_ke' => $mingguKe,
                            'modul_id' => $modul->id,
                            'kelompok_id' => $kelompok->id,
                            'jam_mulai' => $jamMulai,
                            'jam_selesai' => $jamSelesai,
                        ],
                        [
                            'kelompok_label' => $kelompok->nama_kelompok,
                            'keterangan' => null,
                        ]
                    );
                }
            }

            $this->command?->info("Import jadwal modul selesai: {$prodi}");
        }
    }

    private function getOrCreateKelompok(
        Praktikum $praktikum,
        string $prodi,
        int $nomorKelompok,
        int $nomorSesi
    ): Kelompok {
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

    private function getOrCreatePraktikan(
        Praktikum $praktikum,
        string $prodi,
        string $nim,
        string $nama,
        Kelompok $kelompok
    ): Praktikan {
        $email = 'praktikan.' . $nim . '@fisdas.test';

        $user = User::updateOrCreate(
            [
                'nim' => $nim,
            ],
            [
                'name' => $nama,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'praktikan',
                'prodi' => $prodi,
                'jenis_kelamin' => $this->guessGenderFromName($nama),
                'nomor_ponsel' => null,
                'auth_provider' => 'email',
                'profile_completed' => true,
            ]
        );

        $praktikan = Praktikan::firstOrNew([
            'user_id' => $user->id,
            'praktikum_id' => $praktikum->id,
        ]);

        $praktikan->kelompok_id = $kelompok->id;

        if (!$praktikan->qr_code) {
            $praktikan->qr_code = 'QR-PRAKTIKAN-' . $praktikum->id . '-' . $nim . '-' . strtoupper(Str::random(8));
        }

        $praktikan->save();

        return $praktikan;
    }

    private function sesiDariNomorKelompok(int $nomorKelompok): int
    {
        if ($nomorKelompok <= 8) {
            return 1;
        }

        if ($nomorKelompok <= 16) {
            return 2;
        }

        return 3;
    }

    private function dataPath(string $filename): string
    {
        return database_path('seeders/data/' . $filename);
    }

    private function cellValue($sheet, int $col, int $row)
    {
        $cellAddress = Coordinate::stringFromColumnIndex($col) . $row;

        return $sheet->getCell($cellAddress)->getValue();
    }

    private function bersihkanTeks($value): string
    {
        return trim(preg_replace('/\s+/', ' ', (string) $value));
    }

    private function bersihkanNim($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (is_numeric($value)) {
            return (string) (int) $value;
        }

        $value = trim((string) $value);
        $value = preg_replace('/\.0$/', '', $value);

        return preg_replace('/\D/', '', $value);
    }

    private function nimValid(?string $nim): bool
    {
        return $nim !== null && preg_match('/^\d{8,15}$/', $nim);
    }

    private function nomorDariTeks($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        preg_match('/\d+/', (string) $value, $matches);

        return isset($matches[0]) ? (int) $matches[0] : null;
    }

    private function adaRentangJam(string $value): bool
    {
        return preg_match('/\d{1,2}[\.:]\d{2}\s*-\s*\d{1,2}[\.:]\d{2}/', $value) === 1;
    }

    private function pecahRentangJam(string $value): array
    {
        preg_match('/(\d{1,2})[\.:](\d{2})\s*-\s*(\d{1,2})[\.:](\d{2})/', $value, $matches);

        if (!$matches) {
            return [null, null];
        }

        $jamMulai = str_pad($matches[1], 2, '0', STR_PAD_LEFT) . ':' . $matches[2] . ':00';
        $jamSelesai = str_pad($matches[3], 2, '0', STR_PAD_LEFT) . ':' . $matches[4] . ':00';

        return [$jamMulai, $jamSelesai];
    }

    private function isMingguLabel(string $value): bool
    {
        return str_contains(strtolower($value), 'minggu');
    }

    private function nomorMinggu(string $value): ?int
    {
        $value = strtolower($value);

        if (str_contains($value, 'i') && !str_contains($value, 'ii')) {
            return 1;
        }

        if (str_contains($value, 'ii') && !str_contains($value, 'iii')) {
            return 2;
        }

        if (str_contains($value, 'iii') && !str_contains($value, 'iv')) {
            return 3;
        }

        if (str_contains($value, 'iv')) {
            return 4;
        }

        if (str_contains($value, 'v')) {
            return 5;
        }

        return $this->nomorDariTeks($value);
    }

    private function prodiDariNamaSheet(string $sheet): ?string
    {
        $sheet = strtolower($sheet);

        return match (true) {
            str_contains($sheet, 'elektro') => 'Teknik Elektro',
            str_contains($sheet, 'geologi') => 'Teknik Geologi',
            str_contains($sheet, 'industri') => 'Teknik Industri',
            str_contains($sheet, 'lingkungan') => 'Teknik Lingkungan',
            str_contains($sheet, 'tambang') || str_contains($sheet, 'pertambangan') => 'Teknik Pertambangan',
            default => null,
        };
    }

    private function prodiDariLabelJadwalLaporan(string $label): array
    {
        $lower = strtolower($label);

        if (str_contains($lower, 'elektro')) {
            return ['Teknik Elektro', $label];
        }

        if (str_contains($lower, 'geologi')) {
            return ['Teknik Geologi', $label];
        }

        if (str_contains($lower, 'industri')) {
            return ['Teknik Industri', $label];
        }

        if (str_contains($lower, 'lingkungan')) {
            return ['Teknik Lingkungan', $label];
        }

        if (str_contains($lower, 'tambang') || str_contains($lower, 'pertambangan')) {
            return ['Teknik Pertambangan', $label];
        }

        return [null, $label];
    }

    private function guessGenderFromName(string $name): string
    {
        $nameLower = strtolower(trim($name));

        $femaleKeywords = [
            'putri', 'nur', 'siti', 'ayu', 'anisa', 'annisa', 'rahma', 'rahmah', 'zahra', 'nabila',
            'nabillah', 'amelia', 'amalia', 'fatimah', 'fitri', 'fitriani', 'lestari', 'wulandari',
            'novita', 'dewi', 'dwi lestari', 'indah', 'suci', 'safitri', 'khairunnisa', 'khairunisa',
            'nadia', 'nadya', 'tiara', 'angel', 'syalom', 'grace', 'claudia', 'mariana', 'maria',
            'elizabeth', 'agnes', 'theresia', 'cynthia', 'sherly', 'vania', 'tasya', 'syifa', 'aulia',
            'dinda', 'cantika', 'maya', 'widya', 'ratna', 'puspa', 'anggraeni', 'anggraini', 'kusuma',
            'intan', 'mutiara', 'salwa', 'farah', 'syahira', 'alya', 'aura', 'berlian', 'chelsea',
            'dian', 'erika', 'fadilla', 'fadilah', 'gina', 'hana', 'hasna', 'ica', 'isya', 'jasmine',
            'karina', 'laras', 'maharani', 'mey', 'nayla', 'olivia', 'pratiwi', 'qonita', 'rani',
            'salsabila', 'tania', 'ulfa', 'vivi', 'windy', 'yulia', 'zahira', 'nurlina', 'marisa',
            'nadira', 'shafa', 'karimah', 'hildayanti', 'jihan', 'najwa', 'talitha', 'adinda'
        ];

        $maleKeywords = [
            'muhammad', 'mohammad', 'mochammad', 'muhamad', 'ahmad', 'achmad', 'fadil', 'fadillah',
            'rizky', 'rizki', 'rizaldi', 'saiful', 'budi', 'bayu', 'aditya', 'anang', 'irfan',
            'agung', 'bagus', 'dimas', 'fajar', 'hendra', 'ilham', 'joko', 'krisna', 'lukman',
            'mahendra', 'naufal', 'oscar', 'pratama', 'putra', 'ridho', 'satria', 'taufiq', 'taufik',
            'wahyu', 'yusuf', 'zaki', 'danendra', 'danapati', 'mouses', 'rinaldi', 'syihabudin',
            'almas', 'sabil', 'gafur', 'fathir', 'rozzaq', 'setiawan', 'akbar', 'dzakiy', 'haidar',
            'bima', 'gilang', 'hafiz', 'ihsan', 'kevin', 'luthfi', 'miftah', 'najib', 'pandu',
            'raihan', 'rendy', 'syahrul', 'tegar', 'vincensius', 'wildan', 'yordan', 'zulfikar'
        ];

        foreach ($femaleKeywords as $kw) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $nameLower)) {
                return 'P';
            }
        }

        foreach ($maleKeywords as $kw) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $nameLower)) {
                return 'L';
            }
        }

        return (crc32($name) % 2 === 0) ? 'L' : 'P';
    }
}