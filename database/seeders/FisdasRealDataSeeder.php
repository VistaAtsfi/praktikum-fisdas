<?php

namespace Database\Seeders;

use App\Models\Asisten;
use App\Models\AsistenPemeriksaLaporan;
use App\Models\Kelompok;
use App\Models\Modul;
use App\Models\NilaiAkhir;
use App\Models\NilaiHarian;
use App\Models\NilaiKemampuanPraktikum;
use App\Models\NilaiLaporanResmi;
use App\Models\NilaiPenulisanLaporan;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\PraktikumProdi;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class FisdasRealDataSeeder extends Seeder
{
    private array $modulFisdas1 = [
        1 => 'Hukum Kekekalan Energi Mekanik',
        2 => 'Pengukuran Menggunakan Multimeter',
        3 => 'Simulasi Pencerminan',
        4 => 'Gerak Jatuh Bebas',
        5 => 'Resonansi dan Osilasi pada Pegas Helik',
        6 => 'Rangkaian Seri-Paralel',
        7 => 'Bayangan, Cermin, Warna dan Pembiasan',
        8 => 'Hambatan dalam Baterai dan Rangkaian Pembagi Tegangan',
    ];

    private array $modulFisdas2 = [
        1 => 'Modul Fisika Dasar 2 - 1',
        2 => 'Modul Fisika Dasar 2 - 2',
        3 => 'Modul Fisika Dasar 2 - 3',
        4 => 'Modul Fisika Dasar 2 - 4',
        5 => 'Modul Fisika Dasar 2 - 5',
        6 => 'Modul Fisika Dasar 2 - 6',
    ];

    public function run(): void
    {
        $this->command?->info('Mulai import data real Fisdas...');

        $fisdas1 = Praktikum::updateOrCreate(
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

        $fisdas2 = Praktikum::updateOrCreate(
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

        $this->seedModul($fisdas1, $this->modulFisdas1);
        $this->seedModul($fisdas2, $this->modulFisdas2);

        $prodis = [
            'Teknik Elektro',
            'Teknik Geologi',
            'Teknik Industri',
            'Teknik Lingkungan',
            'Teknik Pertambangan',
        ];

        foreach ([$fisdas1, $fisdas2] as $praktikum) {
            foreach ($prodis as $prodi) {
                PraktikumProdi::updateOrCreate(
                    [
                        'praktikum_id' => $praktikum->id,
                        'nama_prodi' => $prodi,
                    ],
                    []
                );
            }
        }

        $this->importNilaiWorkbook($fisdas1, 'Teknik Elektro', 'Copy of Input Nilai Elektro.xlsx');
        $this->importNilaiWorkbook($fisdas1, 'Teknik Geologi', 'Copy of Input Nilai Geologi.xlsx');
        $this->importNilaiWorkbook($fisdas1, 'Teknik Industri', 'Copy of Input Nilai Industri.xlsx');
        $this->importNilaiWorkbook($fisdas1, 'Teknik Lingkungan', 'Copy of Input Nilai Lingkungan.xlsx');
        $this->importNilaiWorkbook($fisdas1, 'Teknik Pertambangan', 'Copy of Input Nilai Pertambangan.xlsx');

        $this->importNilaiAkhirSimpleWorkbook($fisdas1, 'Teknik Elektro', 'Copy of Nilai Fisika Dasar Teknik Elektro TA Ganjil 2025-2026.xlsx');
        $this->importNilaiAkhirSimpleWorkbook($fisdas1, 'Teknik Geologi', 'Copy of Nilai Fisika Dasar Teknik Geologi TA Ganjil 2025-2026.xlsx');
        $this->importNilaiAkhirSimpleWorkbook($fisdas1, 'Teknik Industri', 'Copy of Nilai Fisika Dasar Teknik Industri TA Ganjil 2025-2026.xlsx');
        $this->importNilaiAkhirSimpleWorkbook($fisdas1, 'Teknik Lingkungan', 'Copy of Nilai Fisika Dasar Teknik Lingkungan TA Ganjil 2025-2026.xlsx');
        $this->importNilaiAkhirSimpleWorkbook($fisdas1, 'Teknik Pertambangan', 'Copy of Nilai Fisika Dasar Teknik Pertambangan TA Ganjil 2025-2026.xlsx');

        $this->importNilaiWorkbook($fisdas2, 'Teknik Elektro', 'Copy of Fisika Dasar II Teknik Elektro.xlsx');
        $this->importNilaiWorkbook($fisdas2, 'Teknik Geologi', 'Copy of Fisika Dasar II Teknik Geologi.xlsx');
        $this->importNilaiWorkbook($fisdas2, 'Teknik Industri', 'Copy of Fisika Dasar II Teknik Industri.xlsx');

        $this->importPresensiWorkbook($fisdas1, 'Teknik Elektro', 'Copy of ABSENSI PRAKTIKUM FISIKA DASAR TE (Responses).xlsx');
        $this->importPresensiWorkbook($fisdas1, 'Teknik Geologi', 'Copy of ABSENSI PRAKTIKUM FISIKA DASAR TG (Responses).xlsx');
        $this->importPresensiWorkbook($fisdas1, 'Teknik Industri', 'Copy of ABSENSI PRAKTIKUM FISIKA DASAR TI (Responses).xlsx');
        $this->importPresensiWorkbook($fisdas1, 'Teknik Lingkungan', 'Copy of ABSENSI PRAKTIKUM FISIKA DASAR TL (Jawaban).xlsx');
        $this->importPresensiWorkbook($fisdas1, 'Teknik Pertambangan', 'Copy of ABSENSI PRAKTIKUM FISIKA DASAR TP (Responses).xlsx');

        $this->importPresensiWorkbook($fisdas2, 'Teknik Elektro', 'Copy of ABSEN PRAKTIKUM FISDAS II-TE (Responses).xlsx');
        $this->importPresensiWorkbook($fisdas2, 'Teknik Geologi', 'Copy of ABSEN PRAKTIKUM FISDAS II-TG (Responses).xlsx');
        $this->importPresensiWorkbook($fisdas2, 'Teknik Industri', 'Copy of ABSEN PRAKTIKUM FISDAS II-TI (Responses).xlsx');

        $this->importAsistenPemeriksa($fisdas1, 'Asisten Pemeriksa Fisdas Ganjil 2025.xlsx');

        $this->command?->info('Import selesai.');
        $this->command?->info('Total users: ' . User::count());
        $this->command?->info('Total praktikan: ' . Praktikan::count());
        $this->command?->info('Total asisten: ' . Asisten::count());
        $this->command?->info('Total kelompok: ' . Kelompok::count());
        $this->command?->info('Total presensi: ' . Presensi::count());
        $this->command?->info('Total nilai akhir: ' . NilaiAkhir::count());
    }

    private function seedModul(Praktikum $praktikum, array $moduls): void
    {
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

    private function jumlahModul(Praktikum $praktikum): int
    {
        return $praktikum->jenis_praktikum === 'fisdas_2' ? 6 : 8;
    }

    private function importNilaiWorkbook(Praktikum $praktikum, string $prodi, string $filename): void
    {
        $path = $this->dataPath($filename);

        if (!file_exists($path)) {
            $this->command?->warn("File nilai tidak ditemukan: {$filename}");
            return;
        }

        $spreadsheet = IOFactory::load($path);

        $this->importPesertaSheet($spreadsheet, $praktikum, $prodi);
        $this->importLaporanResmiSheet($spreadsheet, $praktikum);
        $this->importPenulisanLaporanSheet($spreadsheet, $praktikum);
        $this->importKemampuanPraktikumSheet($spreadsheet, $praktikum);
        $this->importNilaiHarianSheet($spreadsheet, $praktikum);
        $this->importNilaiAkhirSheet($spreadsheet, $praktikum);

        $this->command?->info("Import nilai selesai: {$filename}");
    }

    private function importPesertaSheet($spreadsheet, Praktikum $praktikum, string $prodi): void
    {
        $sheet = $spreadsheet->getSheetByName('INPUT DAFTAR PESERTA');

        if (!$sheet) {
            $this->command?->warn('Sheet INPUT DAFTAR PESERTA tidak ditemukan.');
            return;
        }

        $rowTerakhir = $sheet->getHighestDataRow();
        $urutan = 0;

        for ($row = 1; $row <= $rowTerakhir; $row++) {
            $nim = $this->bersihkanNim($this->cellValue($sheet, 2, $row));
            $nama = $this->bersihkanTeks($this->cellValue($sheet, 3, $row));

            if (!$this->nimValid($nim) || $nama === '') {
                continue;
            }

            $urutan++;

            $kelompokNomor = (int) ceil($urutan / 4);
            $sesiNomor = $kelompokNomor <= 8 ? 1 : 2;

            $kelompok = $this->getOrCreateKelompok($praktikum, $prodi, $kelompokNomor, $sesiNomor);

            $this->getOrCreatePraktikan(
                praktikum: $praktikum,
                prodi: $prodi,
                nim: $nim,
                nama: $nama,
                kelompok: $kelompok
            );
        }
    }

    private function importLaporanResmiSheet($spreadsheet, Praktikum $praktikum): void
    {
        $sheet = $spreadsheet->getSheetByName('LAPORAN RESMI');

        if (!$sheet) {
            return;
        }

        $jumlahModul = $this->jumlahModul($praktikum);
        $moduls = $this->modulMap($praktikum);
        $rowTerakhir = $sheet->getHighestDataRow();

        for ($row = 1; $row <= $rowTerakhir; $row++) {
            $nim = $this->bersihkanNim($this->cellValue($sheet, 2, $row));

            if (!$this->nimValid($nim)) {
                continue;
            }

            $praktikan = $this->findPraktikanByNim($praktikum, $nim);

            if (!$praktikan) {
                continue;
            }

            $nilaiModul = [];

            for ($nomor = 1; $nomor <= $jumlahModul; $nomor++) {
                $modul = $moduls[$nomor] ?? null;

                if (!$modul) {
                    continue;
                }

                $nilai = $this->angka($this->cellCalculated($sheet, 3 + $nomor, $row));

                if ($nilai !== null) {
                    $nilaiModul[(string) $modul->id] = $nilai;
                }
            }

            NilaiLaporanResmi::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'nilai_modul' => $nilaiModul,
                    'rata_rata' => $this->rataRata($nilaiModul),
                ]
            );
        }
    }

    private function importPenulisanLaporanSheet($spreadsheet, Praktikum $praktikum): void
    {
        $sheet = $spreadsheet->getSheetByName('PENULISAN LAPORAN');

        if (!$sheet) {
            return;
        }

        $jumlahModul = $this->jumlahModul($praktikum);
        $moduls = $this->modulMap($praktikum);
        $rowTerakhir = $sheet->getHighestDataRow();

        for ($row = 1; $row <= $rowTerakhir; $row++) {
            $nim = $this->bersihkanNim($this->cellValue($sheet, 2, $row));

            if (!$this->nimValid($nim)) {
                continue;
            }

            $praktikan = $this->findPraktikanByNim($praktikum, $nim);

            if (!$praktikan) {
                continue;
            }

            $laporanMingguan = [];

            for ($nomor = 1; $nomor <= $jumlahModul; $nomor++) {
                $modul = $moduls[$nomor] ?? null;

                if (!$modul) {
                    continue;
                }

                $nilai = $this->angka($this->cellCalculated($sheet, 3 + $nomor, $row));

                if ($nilai !== null) {
                    $laporanMingguan[(string) $modul->id] = $nilai;
                }
            }

            $rataMingguan = $this->rataRata($laporanMingguan);

            $rataResmi = NilaiLaporanResmi::where('praktikum_id', $praktikum->id)
                ->where('praktikan_id', $praktikan->id)
                ->value('rata_rata');

            NilaiPenulisanLaporan::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'laporan_mingguan' => $laporanMingguan,
                    'rata_rata_mingguan' => $rataMingguan,
                    'rata_rata_resmi' => $rataResmi,
                    'nilai_akhir' => $this->rataRata([
                        $rataMingguan,
                        $rataResmi !== null ? (float) $rataResmi : null,
                    ]),
                ]
            );
        }
    }

    private function importKemampuanPraktikumSheet($spreadsheet, Praktikum $praktikum): void
    {
        $sheet = $spreadsheet->getSheetByName('KEMAMPUAN PRAKTIKUM');

        if (!$sheet) {
            return;
        }

        $jumlahModul = $this->jumlahModul($praktikum);
        $moduls = $this->modulMap($praktikum);
        $rowTerakhir = $sheet->getHighestDataRow();

        for ($row = 1; $row <= $rowTerakhir; $row++) {
            $nim = $this->bersihkanNim($this->cellValue($sheet, 2, $row));

            if (!$this->nimValid($nim)) {
                continue;
            }

            $praktikan = $this->findPraktikanByNim($praktikum, $nim);

            if (!$praktikan) {
                continue;
            }

            $nilaiModul = [];

            for ($nomor = 1; $nomor <= $jumlahModul; $nomor++) {
                $modul = $moduls[$nomor] ?? null;

                if (!$modul) {
                    continue;
                }

                $startCol = 4 + (($nomor - 1) * 5);

                $k1 = $this->angka($this->cellCalculated($sheet, $startCol, $row));
                $k2 = $this->angka($this->cellCalculated($sheet, $startCol + 1, $row));
                $k3 = $this->angka($this->cellCalculated($sheet, $startCol + 2, $row));
                $k4 = $this->angka($this->cellCalculated($sheet, $startCol + 3, $row));
                $rerata = $this->angka($this->cellCalculated($sheet, $startCol + 4, $row));

                if ($k1 === null && $k2 === null && $k3 === null && $k4 === null && $rerata === null) {
                    continue;
                }

                $nilaiModul[(string) $modul->id] = [
                    'k1' => $k1,
                    'k2' => $k2,
                    'k3' => $k3,
                    'k4' => $k4,
                    'rerata' => $rerata,
                ];
            }

            $rerataAkhir = collect($nilaiModul)
                ->pluck('rerata')
                ->filter(fn ($nilai) => $nilai !== null)
                ->avg();

            NilaiKemampuanPraktikum::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'nilai_modul' => $nilaiModul,
                    'rata_rata_akhir' => $rerataAkhir !== null ? round((float) $rerataAkhir, 2) : null,
                ]
            );
        }
    }

    private function importNilaiHarianSheet($spreadsheet, Praktikum $praktikum): void
    {
        $sheet = $spreadsheet->getSheetByName('NILAI HARIAN');

        if (!$sheet) {
            return;
        }

        $jumlahModul = $this->jumlahModul($praktikum);
        $moduls = $this->modulMap($praktikum);
        $rowTerakhir = $sheet->getHighestDataRow();

        $kehadiranStart = 4;
        $kehadiranRataCol = $kehadiranStart + $jumlahModul;
        $laporanCol = $kehadiranRataCol + 1;

        $responsiStart = $laporanCol + 1;
        $responsiRataCol = $responsiStart + $jumlahModul;

        $asistensiStart = $responsiRataCol + 1;
        $asistensiRataCol = $asistensiStart + $jumlahModul;

        $kemampuanStart = $asistensiRataCol + 1;
        $kemampuanRataCol = $kemampuanStart + $jumlahModul;

        for ($row = 1; $row <= $rowTerakhir; $row++) {
            $nim = $this->bersihkanNim($this->cellValue($sheet, 2, $row));

            if (!$this->nimValid($nim)) {
                continue;
            }

            $praktikan = $this->findPraktikanByNim($praktikum, $nim);

            if (!$praktikan) {
                continue;
            }

            $kehadiran = [];
            $responsi = [];
            $asistensi = [];
            $kemampuanPraktik = [];

            for ($nomor = 1; $nomor <= $jumlahModul; $nomor++) {
                $modul = $moduls[$nomor] ?? null;

                if (!$modul) {
                    continue;
                }

                $modulId = (string) $modul->id;

                $nilaiKehadiran = $this->angka($this->cellCalculated($sheet, $kehadiranStart + ($nomor - 1), $row));
                $nilaiResponsi = $this->angka($this->cellCalculated($sheet, $responsiStart + ($nomor - 1), $row));
                $nilaiAsistensi = $this->angka($this->cellCalculated($sheet, $asistensiStart + ($nomor - 1), $row));
                $nilaiKemampuan = $this->angka($this->cellCalculated($sheet, $kemampuanStart + ($nomor - 1), $row));

                if ($nilaiKehadiran !== null) {
                    $kehadiran[$modulId] = $nilaiKehadiran >= 1 ? 'hadir' : 'alpa';
                }

                if ($nilaiResponsi !== null) {
                    $responsi[$modulId] = $nilaiResponsi;
                }

                if ($nilaiAsistensi !== null) {
                    $asistensi[$modulId] = $nilaiAsistensi;
                }

                if ($nilaiKemampuan !== null) {
                    $kemampuanPraktik[$modulId] = $nilaiKemampuan;
                }
            }

            NilaiHarian::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'kehadiran' => $kehadiran,
                    'rata_rata_kehadiran' => $this->angka($this->cellCalculated($sheet, $kehadiranRataCol, $row)),
                    'laporan' => $this->angka($this->cellCalculated($sheet, $laporanCol, $row)),
                    'responsi' => $responsi,
                    'rata_rata_responsi' => $this->angka($this->cellCalculated($sheet, $responsiRataCol, $row)),
                    'asistensi' => $asistensi,
                    'rata_rata_asistensi' => $this->angka($this->cellCalculated($sheet, $asistensiRataCol, $row)),
                    'kemampuan_praktik' => $kemampuanPraktik,
                    'rata_rata_kemampuan_praktik' => $this->angka($this->cellCalculated($sheet, $kemampuanRataCol, $row)),
                ]
            );
        }
    }

    private function importNilaiAkhirSheet($spreadsheet, Praktikum $praktikum): void
    {
        $sheet = $spreadsheet->getSheetByName('NILAI AKHIR');

        if (!$sheet) {
            return;
        }

        $rowTerakhir = $sheet->getHighestDataRow();

        for ($row = 1; $row <= $rowTerakhir; $row++) {
            $nim = $this->bersihkanNim($this->cellValue($sheet, 2, $row));

            if (!$this->nimValid($nim)) {
                continue;
            }

            $praktikan = $this->findPraktikanByNim($praktikum, $nim);

            if (!$praktikan) {
                continue;
            }

            $nilaiAngka = $this->angka($this->cellCalculated($sheet, 9, $row));
            $nilaiBobot = $this->angka($this->cellCalculated($sheet, 10, $row));
            $nilaiHuruf = $this->bersihkanTeks($this->cellCalculated($sheet, 11, $row));

            if ($nilaiHuruf === '' && $nilaiAngka !== null) {
                $nilaiHuruf = $this->konversiHuruf($nilaiAngka);
            }

            NilaiAkhir::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'kehadiran' => $this->angka($this->cellCalculated($sheet, 4, $row)),
                    'laporan' => $this->angka($this->cellCalculated($sheet, 5, $row)),
                    'responsi' => $this->angka($this->cellCalculated($sheet, 6, $row)),
                    'asistensi' => $this->angka($this->cellCalculated($sheet, 7, $row)),
                    'kemampuan_praktik' => $this->angka($this->cellCalculated($sheet, 8, $row)),
                    'nilai_angka' => $nilaiAngka,
                    'nilai_bobot' => $nilaiBobot ?? $this->konversiBobot($nilaiAngka),
                    'nilai_huruf' => $nilaiHuruf !== '' ? $nilaiHuruf : null,
                ]
            );
        }
    }

    private function importNilaiAkhirSimpleWorkbook(Praktikum $praktikum, string $prodi, string $filename): void
    {
        $path = $this->dataPath($filename);

        if (!file_exists($path)) {
            $this->command?->warn("File nilai akhir tidak ditemukan: {$filename}");
            return;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);
        $rowTerakhir = $sheet->getHighestDataRow();

        for ($row = 1; $row <= $rowTerakhir; $row++) {
            $nim = $this->bersihkanNim($this->cellValue($sheet, 2, $row));
            $nama = $this->bersihkanTeks($this->cellValue($sheet, 3, $row));

            if (!$this->nimValid($nim)) {
                continue;
            }

            $nilaiAngka = $this->angka($this->cellCalculated($sheet, 4, $row));
            $nilaiHuruf = $this->bersihkanTeks($this->cellValue($sheet, 5, $row));

            if ($nilaiAngka === null && $nilaiHuruf === '') {
                continue;
            }

            $praktikan = $this->findPraktikanByNim($praktikum, $nim);

            if (!$praktikan) {
                $kelompok = $this->getOrCreateKelompok($praktikum, $prodi, 1, 1);

                $praktikan = $this->getOrCreatePraktikan(
                    praktikum: $praktikum,
                    prodi: $prodi,
                    nim: $nim,
                    nama: $nama !== '' ? $nama : 'Praktikan ' . $nim,
                    kelompok: $kelompok
                );
            }

            if ($nilaiHuruf === '' && $nilaiAngka !== null) {
                $nilaiHuruf = $this->konversiHuruf($nilaiAngka);
            }

            NilaiAkhir::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'nilai_angka' => $nilaiAngka,
                    'nilai_bobot' => $this->konversiBobot($nilaiAngka),
                    'nilai_huruf' => $nilaiHuruf !== '' ? $nilaiHuruf : null,
                ]
            );
        }

        $this->command?->info("Import nilai akhir selesai: {$filename}");
    }

    private function importPresensiWorkbook(Praktikum $praktikum, string $prodi, string $filename): void
    {
        $path = $this->dataPath($filename);

        if (!file_exists($path)) {
            $this->command?->warn("File presensi tidak ditemukan: {$filename}");
            return;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheet(0);

        $rowTerakhir = $sheet->getHighestDataRow();
        $headers = $this->headerMap($sheet);

        $tanggalKeModul = [];
        $counterTanggal = 0;

        for ($row = 2; $row <= $rowTerakhir; $row++) {
            $nim = $this->bersihkanNim($this->cellByHeader($sheet, $headers, $row, [
                'Nim',
                'NIM',
                'nim',
            ]));

            if (!$this->nimValid($nim)) {
                continue;
            }

            $nama = $this->bersihkanTeks($this->cellByHeader($sheet, $headers, $row, [
                'Nama',
                'nama',
            ]));

            $email = $this->bersihkanTeks($this->cellByHeader($sheet, $headers, $row, [
                'Email Address',
                'Email',
                'email',
            ]));

            $timestamp = $this->parseTanggal($this->cellByHeader($sheet, $headers, $row, [
                'Timestamp',
                'timestamp',
            ]));

            $kelompokNomor = $this->nomorDariTeks($this->cellByHeader($sheet, $headers, $row, [
                'Kelompok',
                'kelompok',
            ]));

            $sesiNomor = $this->nomorDariTeks($this->cellByHeader($sheet, $headers, $row, [
                'Sesi Praktikum',
                'Sesi',
                'sesi',
            ]));

            $statusRaw = $this->cellByHeader($sheet, $headers, $row, [
                'Status Kehadiran',
                'Status',
                'status',
            ]);

            $statusPresensi = $this->statusPresensi($statusRaw);
            $statusNilaiHarian = $this->statusNilaiHarian($statusRaw);

            if (!$kelompokNomor) {
                $kelompokNomor = 1;
            }

            if (!$sesiNomor) {
                $sesiNomor = $kelompokNomor <= 8 ? 1 : 2;
            }

            $kelompok = $this->getOrCreateKelompok($praktikum, $prodi, $kelompokNomor, $sesiNomor);

            $praktikan = $this->getOrCreatePraktikan(
                praktikum: $praktikum,
                prodi: $prodi,
                nim: $nim,
                nama: $nama !== '' ? $nama : 'Praktikan ' . $nim,
                kelompok: $kelompok
            );

            $tanggalKey = $timestamp?->format('Y-m-d') ?? 'tanpa-tanggal-' . $row;

            if (!isset($tanggalKeModul[$tanggalKey])) {
                $counterTanggal++;
                $tanggalKeModul[$tanggalKey] = (($counterTanggal - 1) % $this->jumlahModul($praktikum)) + 1;
            }

            $nomorModul = $tanggalKeModul[$tanggalKey];

            $modul = Modul::where('praktikum_id', $praktikum->id)
                ->where('nomor_modul', $nomorModul)
                ->first();

            if (!$modul) {
                continue;
            }

            $asisten = $this->getOrCreateAsistenByEmail(
                praktikum: $praktikum,
                email: $email !== '' ? $email : 'asisten.presensi.' . $praktikum->id . '@fisdas.test'
            );

            Presensi::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                    'modul_id' => $modul->id,
                    'sesi' => 'Sesi ' . $sesiNomor,
                ],
                [
                    'asisten_id' => $asisten?->id,
                    'waktu_absen' => $timestamp ?? now(),
                    'email' => $email,
                    'nim' => $nim,
                    'nama' => $praktikan->user->name ?? $nama,
                    'kelompok' => $kelompok->nama_kelompok,
                    'qr_code_scanned' => $praktikan->qr_code,
                    'status' => $statusPresensi,
                    'keterangan' => 'Data presensi hasil import dari file ' . $filename,
                ]
            );

            $this->sinkronKehadiranNilaiHarian($praktikum, $praktikan, $modul, $statusNilaiHarian);
        }

        $this->command?->info("Import presensi selesai: {$filename}");
    }

    private function importAsistenPemeriksa(Praktikum $praktikum, string $filename): void
    {
        $path = $this->dataPath($filename);

        if (!file_exists($path)) {
            $this->command?->warn("File asisten pemeriksa tidak ditemukan: {$filename}");
            return;
        }

        $spreadsheet = IOFactory::load($path);

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $prodi = $this->prodiDariNamaSheet($sheet->getTitle());

            if (!$prodi) {
                continue;
            }

            $rowTerakhir = $sheet->getHighestDataRow();

            for ($row = 7; $row <= $rowTerakhir; $row++) {
                $kelompokNomor = $this->nomorDariTeks($this->cellValue($sheet, 2, $row));

                if (!$kelompokNomor) {
                    continue;
                }

                $kelompok = $this->getOrCreateKelompok(
                    praktikum: $praktikum,
                    prodi: $prodi,
                    nomorKelompok: $kelompokNomor,
                    nomorSesi: $kelompokNomor <= 8 ? 1 : 2
                );

                for ($nomorModul = 1; $nomorModul <= 8; $nomorModul++) {
                    $namaAsisten = $this->bersihkanTeks(
                        $this->cellValue($sheet, 2 + $nomorModul, $row)
                    );

                    if ($namaAsisten === '') {
                        continue;
                    }

                    $asisten = $this->getOrCreateAsistenByName($praktikum, $namaAsisten);

                    $modul = Modul::where('praktikum_id', $praktikum->id)
                        ->where('nomor_modul', $nomorModul)
                        ->first();

                    if (!$modul) {
                        continue;
                    }

                    AsistenPemeriksaLaporan::updateOrCreate(
                        [
                            'praktikum_id' => $praktikum->id,
                            'kelompok_id' => $kelompok->id,
                            'modul_id' => $modul->id,
                        ],
                        [
                            'asisten_id' => $asisten->id,
                            'prodi' => $prodi,
                        ]
                    );
                }
            }
        }

        $this->command?->info("Import asisten pemeriksa selesai: {$filename}");
    }

    private function sinkronKehadiranNilaiHarian(
        Praktikum $praktikum,
        Praktikan $praktikan,
        Modul $modul,
        string $status
    ): void {
        $nilaiHarian = NilaiHarian::firstOrNew([
            'praktikum_id' => $praktikum->id,
            'praktikan_id' => $praktikan->id,
        ]);

        $kehadiran = $nilaiHarian->kehadiran ?? [];
        $kehadiran[(string) $modul->id] = $status;

        $nilaiHarian->kehadiran = $kehadiran;
        $nilaiHarian->rata_rata_kehadiran = $this->hitungRataKehadiran($kehadiran, $praktikum);
        $nilaiHarian->save();
    }

    private function getOrCreatePraktikan(
        Praktikum $praktikum,
        string $prodi,
        string $nim,
        string $nama,
        ?Kelompok $kelompok = null
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
                'jenis_kelamin' => null,
                'nomor_ponsel' => null,
                'auth_provider' => 'email',
                'profile_completed' => true,
            ]
        );

        $praktikan = Praktikan::firstOrNew([
            'user_id' => $user->id,
            'praktikum_id' => $praktikum->id,
        ]);

        if (!$praktikan->exists || !$praktikan->kelompok_id) {
            $praktikan->kelompok_id = $kelompok?->id;
        }

        if (!$praktikan->qr_code) {
            $praktikan->qr_code = 'QR-PRAKTIKAN-' . $praktikum->id . '-' . $nim;
        }

        $praktikan->save();

        return $praktikan;
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

    private function getOrCreateAsistenByName(Praktikum $praktikum, string $nama): Asisten
    {
        $slug = Str::slug($nama);

        if ($slug === '') {
            $slug = 'asisten-' . Str::random(8);
        }

        $email = 'asisten.' . $slug . '@fisdas.test';

        $user = User::updateOrCreate(
            [
                'email' => $email,
            ],
            [
                'name' => $nama,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'asisten',
                'nim' => null,
                'prodi' => 'Asisten Laboratorium',
                'jenis_kelamin' => null,
                'nomor_ponsel' => null,
                'auth_provider' => 'email',
                'profile_completed' => true,
            ]
        );

        return Asisten::updateOrCreate(
            [
                'user_id' => $user->id,
                'praktikum_id' => $praktikum->id,
            ],
            [
                'status' => 'aktif',
            ]
        );
    }

    private function getOrCreateAsistenByEmail(Praktikum $praktikum, string $email): ?Asisten
    {
        $email = trim($email);

        if ($email === '') {
            return null;
        }

        $nama = Str::headline(str_replace(['.', '_', '-'], ' ', explode('@', $email)[0]));

        $user = User::updateOrCreate(
            [
                'email' => $email,
            ],
            [
                'name' => $nama,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'asisten',
                'nim' => null,
                'prodi' => 'Asisten Laboratorium',
                'jenis_kelamin' => null,
                'nomor_ponsel' => null,
                'auth_provider' => 'email',
                'profile_completed' => true,
            ]
        );

        return Asisten::updateOrCreate(
            [
                'user_id' => $user->id,
                'praktikum_id' => $praktikum->id,
            ],
            [
                'status' => 'aktif',
            ]
        );
    }

    private function findPraktikanByNim(Praktikum $praktikum, string $nim): ?Praktikan
    {
        return Praktikan::where('praktikum_id', $praktikum->id)
            ->whereHas('user', fn ($query) => $query->where('nim', $nim))
            ->first();
    }

    private function modulMap(Praktikum $praktikum)
    {
        return Modul::where('praktikum_id', $praktikum->id)
            ->orderBy('nomor_modul')
            ->get()
            ->keyBy('nomor_modul');
    }

    private function headerMap($sheet): array
    {
        $map = [];

        $colTerakhir = $sheet->getHighestDataColumn();
        $colTerakhirIndex = Coordinate::columnIndexFromString($colTerakhir);

        for ($col = 1; $col <= $colTerakhirIndex; $col++) {
            $header = $this->bersihkanTeks($this->cellValue($sheet, $col, 1));

            if ($header !== '') {
                $map[$header] = $col;
            }
        }

        return $map;
    }

    private function cellByHeader($sheet, array $headers, int $row, array $names)
    {
        foreach ($names as $name) {
            if (isset($headers[$name])) {
                return $this->cellValue($sheet, $headers[$name], $row);
            }
        }

        return null;
    }

    private function dataPath(string $filename): string
    {
        return database_path('seeders/data/' . $filename);
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

    private function angka($value): ?float
    {
        if ($value === null || $value === '' || strtoupper((string) $value) === '#NAME?') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return round((float) $value, 2);
    }

    private function rataRata(array $values): ?float
    {
        $values = collect($values)
            ->filter(fn ($value) => $value !== null && $value !== '' && is_numeric($value))
            ->map(fn ($value) => (float) $value);

        if ($values->isEmpty()) {
            return null;
        }

        return round($values->avg(), 2);
    }

    private function parseTanggal($value): ?\DateTime
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject($value);
            } catch (\Throwable $e) {
                return null;
            }
        }

        if (!$value) {
            return null;
        }

        try {
            return new \DateTime((string) $value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function statusPresensi($value): string
    {
        $value = strtolower($this->bersihkanTeks($value));

        return match (true) {
            str_contains($value, 'izin') => 'Izin',
            str_contains($value, 'sakit') => 'Sakit',
            str_contains($value, 'tidak') => 'Alpha',
            str_contains($value, 'alpa') => 'Alpha',
            str_contains($value, 'alpha') => 'Alpha',
            default => 'Hadir',
        };
    }

    private function statusNilaiHarian($value): string
    {
        $value = strtolower($this->bersihkanTeks($value));

        return match (true) {
            str_contains($value, 'izin') => 'izin',
            str_contains($value, 'sakit') => 'sakit',
            str_contains($value, 'tidak') => 'alpa',
            str_contains($value, 'alpa') => 'alpa',
            str_contains($value, 'alpha') => 'alpa',
            default => 'hadir',
        };
    }

    private function hitungRataKehadiran(array $kehadiran, Praktikum $praktikum): ?float
    {
        $jumlahModul = $this->jumlahModul($praktikum);

        if ($jumlahModul <= 0) {
            return null;
        }

        $jumlahHadir = collect($kehadiran)
            ->filter(fn ($status) => strtolower((string) $status) === 'hadir')
            ->count();

        return round(($jumlahHadir / $jumlahModul) * 100, 2);
    }

    private function prodiDariNamaSheet(string $sheet): ?string
    {
        $sheet = strtolower($sheet);

        return match (true) {
            str_contains($sheet, 'elektro') => 'Teknik Elektro',
            str_contains($sheet, 'geologi') => 'Teknik Geologi',
            str_contains($sheet, 'industri') => 'Teknik Industri',
            str_contains($sheet, 'lingkungan') => 'Teknik Lingkungan',
            str_contains($sheet, 'tambang') => 'Teknik Pertambangan',
            default => null,
        };
    }

    private function konversiHuruf(?float $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        return match (true) {
            $nilai >= 80 => 'A',
            $nilai >= 70 => 'B',
            $nilai >= 60 => 'C',
            $nilai >= 50 => 'D',
            default => 'E',
        };
    }

    private function konversiBobot(?float $nilai): ?float
    {
        if ($nilai === null) {
            return null;
        }

        return match (true) {
            $nilai >= 80 => 4.00,
            $nilai >= 70 => 3.50,
            $nilai >= 60 => 2.50,
            $nilai >= 50 => 1.00,
            default => 0.00,
        };
    }

    private function cell($sheet, int $col, int $row)
    {
        $cellAddress = Coordinate::stringFromColumnIndex($col) . $row;

        return $sheet->getCell($cellAddress);
    }

    private function cellValue($sheet, int $col, int $row)
    {
        return $this->cell($sheet, $col, $row)->getValue();
    }

    private function cellCalculated($sheet, int $col, int $row)
    {
        return $this->cell($sheet, $col, $row)->getCalculatedValue();
    }
}