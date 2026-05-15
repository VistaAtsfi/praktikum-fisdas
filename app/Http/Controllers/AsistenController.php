<?php

namespace App\Http\Controllers;

use App\Models\Asisten;
use App\Models\Kelompok;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\NilaiAkhir;
use App\Models\NilaiHarian;
use App\Models\NilaiKemampuanPraktikum;
use App\Models\NilaiLaporanResmi;
use App\Models\NilaiPenulisanLaporan;
use App\Models\Praktikan;
use App\Models\Presensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AsistenController extends Controller
{
    private function pastikanAsisten(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'asisten', 403);
    }

    private function asisten(): ?Asisten
    {
        return Asisten::with('praktikum')
            ->where('user_id', Auth::id())
            ->latest()
            ->first();
    }

    public function beranda()
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        $praktikans = $praktikum
            ? Praktikan::with(['user', 'kelompok', 'nilaiHarian', 'nilaiAkhir'])
                ->where('praktikum_id', $praktikum->id)
                ->latest()
                ->get()
            : collect();

        $kelompoks = $praktikum
            ? Kelompok::where('praktikum_id', $praktikum->id)
                ->orderBy('id')
                ->get()
            : collect();

        $moduls = $praktikum
            ? Modul::where('praktikum_id', $praktikum->id)
                ->orderBy('id')
                ->get()
            : collect();

        $jumlahMateri = $praktikum
            ? Materi::where('praktikum_id', $praktikum->id)->count()
            : 0;

        $jumlahPresensi = $praktikum
            ? Presensi::where('praktikum_id', $praktikum->id)->count()
            : 0;

        $praktikanBelumAsistensi = $praktikans->filter(function ($praktikan) {
            return !$praktikan->nilaiHarian || empty($praktikan->nilaiHarian->rata_rata_asistensi);
        });

        return view('asisten.beranda', compact(
            'asisten',
            'praktikum',
            'praktikans',
            'kelompoks',
            'moduls',
            'jumlahMateri',
            'jumlahPresensi',
            'praktikanBelumAsistensi'
        ));
    }

    public function presensi(Request $request)
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        $moduls = $praktikum
            ? Modul::where('praktikum_id', $praktikum->id)
                ->orderBy('id')
                ->get()
            : collect();

        $kelompoks = $praktikum
            ? Kelompok::where('praktikum_id', $praktikum->id)
                ->orderBy('id')
                ->get()
            : collect();

        $prodis = $praktikum
            ? Praktikan::where('praktikum_id', $praktikum->id)
                ->whereHas('user')
                ->with('user')
                ->get()
                ->pluck('user.prodi')
                ->filter()
                ->unique()
                ->values()
            : collect();

        $sesis = $kelompoks->pluck('sesi')->filter()->unique()->values();

        $rekapScanPresensis = $praktikum && $asisten
        ? Presensi::with('modul')
            ->where('praktikum_id', $praktikum->id)
            ->where('asisten_id', $asisten->id)
            ->where('qr_code_scanned', true)
            ->latest('waktu_absen')
            ->limit(10)
            ->get()
        : collect();

        if (!$praktikum) {
            $praktikans = collect();

            return view('asisten.presensi', compact(
                'asisten',
                'praktikum',
                'praktikans',
                'moduls',
                'kelompoks',
                'prodis',
                'sesis',
                'rekapScanPresensis'
            ));
        }

        $query = Praktikan::with(['user', 'kelompok', 'nilaiHarian', 'nilaiAkhir'])
            ->where('praktikum_id', $praktikum->id);

        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }

        if ($request->filled('prodi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('prodi', $request->prodi);
            });
        }

        if ($request->filled('sesi')) {
            $query->whereHas('kelompok', function ($q) use ($request) {
                $q->where('sesi', $request->sesi);
            });
        }

        $semuaPraktikans = $query
            ->latest()
            ->get();

        $hasilSearch = $this->sequentialSearchPraktikan(
            $semuaPraktikans,
            $request->search
        );

        $praktikans = $this->paginateCollection($hasilSearch, $request, 10);

        return view('asisten.presensi', compact(
            'asisten',
            'praktikum',
            'praktikans',
            'moduls',
            'kelompoks',
            'prodis',
            'sesis',
            'rekapScanPresensis'
        ));
    }

    public function updatePresensi(Request $request)
    {
        $this->pastikanAsisten();

        $request->validate([
            'praktikan_id' => ['required', 'exists:praktikans,id'],
            'modul_id' => ['required', 'exists:moduls,id'],
            'status' => ['nullable', 'in:kosong,hadir,sakit,izin,alpa'],
        ]);

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        abort_if(!$asisten || !$praktikum, 403);

        $praktikan = Praktikan::with(['user', 'kelompok'])
            ->where('praktikum_id', $praktikum->id)
            ->findOrFail($request->praktikan_id);

        $modul = Modul::where('praktikum_id', $praktikum->id)
            ->findOrFail($request->modul_id);

        $status = $request->status === 'kosong' ? null : $request->status;

        $this->simpanStatusPresensi(
            asisten: $asisten,
            praktikumId: $praktikum->id,
            praktikan: $praktikan,
            modul: $modul,
            status: $status,
            qrScanned: false
        );

        return back()->with('status', 'Status presensi berhasil diperbarui.');
    }

    public function scanQr(Request $request): JsonResponse
    {
        $this->pastikanAsisten();

        $request->validate([
            'qr_code' => ['required', 'string'],
        ]);

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        abort_if(!$asisten || !$praktikum, 403);

        $qrCode = trim((string) $request->qr_code);

        $praktikan = Praktikan::with(['user', 'kelompok'])
            ->where('praktikum_id', $praktikum->id)
            ->where('qr_code', $qrCode)
            ->first();

        if (!$praktikan) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak ditemukan atau bukan milik praktikum ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data praktikan berhasil ditemukan.',
            'data' => [
                'qr_code' => $praktikan->qr_code,
                'praktikan_id' => $praktikan->id,
                'timestamp' => now(config('app.timezone'))->format('n/j/Y H:i:s'),
                'email_asisten' => Auth::user()->email,
                'nim' => $praktikan->user->nim ?? '-',
                'nama' => $praktikan->user->name ?? '-',
                'kelompok' => $praktikan->kelompok->nama_kelompok ?? '-',
                'sesi' => $praktikan->kelompok->sesi ?? '-',
                'prodi' => $praktikan->user->prodi ?? '-',
            ],
        ]);
    }

    public function storeScanQr(Request $request): RedirectResponse
    {
        $this->pastikanAsisten();

        $request->validate([
            'qr_code' => ['required', 'string'],
            'modul_id' => ['required', 'exists:moduls,id'],
            'status' => ['required', 'in:hadir,sakit,izin,alpa'],
        ]);

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        abort_if(!$asisten || !$praktikum, 403);

        $praktikan = Praktikan::with(['user', 'kelompok'])
            ->where('praktikum_id', $praktikum->id)
            ->where('qr_code', trim((string) $request->qr_code))
            ->firstOrFail();

        $modul = Modul::where('praktikum_id', $praktikum->id)
            ->findOrFail($request->modul_id);

        $this->simpanStatusPresensi(
            asisten: $asisten,
            praktikumId: $praktikum->id,
            praktikan: $praktikan,
            modul: $modul,
            status: $request->status,
            qrScanned: true
        );

        return redirect()
            ->route('asisten.presensi')
            ->with('status', 'Presensi hasil scan QR berhasil disimpan.');
    }

    public function materi(Request $request)
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        $moduls = $praktikum
            ? Modul::where('praktikum_id', $praktikum->id)
                ->orderBy('id')
                ->get()
            : collect();

        if (!$praktikum) {
            $materis = collect();

            return view('asisten.materi', compact(
                'asisten',
                'praktikum',
                'materis',
                'moduls'
            ));
        }

        $query = Materi::with('modul')
            ->where('praktikum_id', $praktikum->id);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('modul', function ($modulQuery) use ($search) {
                        $modulQuery->where('nama_modul', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('modul_id')) {
            $query->where('modul_id', $request->modul_id);
        }

        if ($request->sort === 'modul_desc') {
            $query->orderBy('modul_id', 'desc')
                ->latest();
        } else {
            $query->orderBy('modul_id', 'asc')
                ->latest();
        }

        $materis = $query->paginate(8)->withQueryString();

        return view('asisten.materi', compact(
            'asisten',
            'praktikum',
            'materis',
            'moduls'
        ));
    }

    public function downloadMateri(Materi $materi): RedirectResponse
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        abort_if(!$asisten || !$praktikum, 403);
        abort_if($materi->praktikum_id !== $praktikum->id, 403);
        abort_if(!$materi->link, 404);

        return redirect()->away($materi->link);
    }

    public function penilaian(Request $request)
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        $kelompoks = $praktikum
            ? Kelompok::where('praktikum_id', $praktikum->id)
                ->orderBy('id')
                ->get()
            : collect();

        if (!$praktikum) {
            $praktikans = collect();

            return view('asisten.penilaian', compact(
                'asisten',
                'praktikum',
                'praktikans',
                'kelompoks'
            ));
        }

        $query = Praktikan::with(['user', 'kelompok', 'nilaiHarian', 'nilaiAkhir'])
            ->where('praktikum_id', $praktikum->id);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }

        if ($request->filled('status_nilai')) {
            if ($request->status_nilai === 'sudah') {
                $query->whereHas('nilaiAkhir');
            }

            if ($request->status_nilai === 'belum') {
                $query->doesntHave('nilaiAkhir');
            }
        }

        $praktikans = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('asisten.penilaian', compact(
            'asisten',
            'praktikum',
            'praktikans',
            'kelompoks'
        ));
    }

    public function beriNilai(Praktikan $praktikan)
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        abort_if(!$asisten || !$praktikum, 403);
        abort_if($praktikan->praktikum_id !== $praktikum->id, 403);

        $praktikan->load([
            'user',
            'kelompok',
            'nilaiHarian',
            'nilaiAkhir',
            'nilaiLaporanResmi',
            'nilaiPenulisanLaporan',
            'nilaiKemampuanPraktikum',
        ]);

        $moduls = Modul::where('praktikum_id', $praktikum->id)
            ->orderBy('id')
            ->get();

        $nilaiHarian = $praktikan->nilaiHarian;
        $nilaiAkhir = $praktikan->nilaiAkhir;
        $nilaiLaporanResmi = $praktikan->nilaiLaporanResmi;
        $nilaiPenulisanLaporan = $praktikan->nilaiPenulisanLaporan;
        $nilaiKemampuanPraktikum = $praktikan->nilaiKemampuanPraktikum;

        return view('asisten.beri-nilai', compact(
            'asisten',
            'praktikum',
            'praktikan',
            'moduls',
            'nilaiHarian',
            'nilaiAkhir',
            'nilaiLaporanResmi',
            'nilaiPenulisanLaporan',
            'nilaiKemampuanPraktikum'
        ));
    }

    public function simpanNilai(Request $request, Praktikan $praktikan)
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        abort_if(!$asisten || !$praktikum, 403);
        abort_if($praktikan->praktikum_id !== $praktikum->id, 403);

        $request->validate([
            'kategori' => ['required', 'in:penulisan_laporan,laporan_resmi,kemampuan_praktik,nilai_harian,nilai_akhir'],
        ]);

        if ($request->kategori === 'penulisan_laporan') {
            $request->validate([
                'laporan_mingguan' => ['nullable', 'array'],
                'laporan_mingguan.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'rata_rata_resmi' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);

            $laporanMingguan = $this->bersihkanNilaiArray($request->input('laporan_mingguan', []));
            $rataMingguan = $this->rataRata($laporanMingguan);
            $rataResmi = $request->filled('rata_rata_resmi') ? (float) $request->rata_rata_resmi : null;
            $nilaiAkhir = $this->rataRata([$rataMingguan, $rataResmi]);

            NilaiPenulisanLaporan::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'laporan_mingguan' => $laporanMingguan,
                    'rata_rata_mingguan' => $rataMingguan,
                    'rata_rata_resmi' => $rataResmi,
                    'nilai_akhir' => $nilaiAkhir,
                ]
            );
        }

        if ($request->kategori === 'laporan_resmi') {
            $request->validate([
                'nilai_modul' => ['nullable', 'array'],
                'nilai_modul.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);

            $nilaiModul = $this->bersihkanNilaiArray($request->input('nilai_modul', []));
            $rataRata = $this->rataRata($nilaiModul);

            NilaiLaporanResmi::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'nilai_modul' => $nilaiModul,
                    'rata_rata' => $rataRata,
                ]
            );
        }

        if ($request->kategori === 'kemampuan_praktik') {
            $request->validate([
                'nilai_modul' => ['nullable', 'array'],
                'nilai_modul.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);

            $nilaiModul = $this->bersihkanNilaiArray($request->input('nilai_modul', []));
            $rataRata = $this->rataRata($nilaiModul);

            NilaiKemampuanPraktikum::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'nilai_modul' => $nilaiModul,
                    'rata_rata_akhir' => $rataRata,
                ]
            );
        }

        if ($request->kategori === 'nilai_harian') {
            $request->validate([
                'kehadiran' => ['nullable', 'array'],
                'kehadiran.*' => ['nullable', 'in:hadir,sakit,izin,alpa'],
                'responsi' => ['nullable', 'array'],
                'responsi.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'asistensi' => ['nullable', 'array'],
                'asistensi.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'kemampuan_praktik' => ['nullable', 'array'],
                'kemampuan_praktik.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'laporan' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);

            $kehadiran = collect($request->input('kehadiran', []))
                ->filter()
                ->map(fn ($value) => strtolower($value))
                ->toArray();

            $responsi = $this->bersihkanNilaiArray($request->input('responsi', []));
            $asistensi = $this->bersihkanNilaiArray($request->input('asistensi', []));
            $kemampuanPraktik = $this->bersihkanNilaiArray($request->input('kemampuan_praktik', []));

            NilaiHarian::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'kehadiran' => $kehadiran,
                    'rata_rata_kehadiran' => $this->hitungRataKehadiran($kehadiran, $praktikum->id),
                    'laporan' => $request->filled('laporan') ? (float) $request->laporan : null,
                    'responsi' => $responsi,
                    'rata_rata_responsi' => $this->rataRata($responsi),
                    'asistensi' => $asistensi,
                    'rata_rata_asistensi' => $this->rataRata($asistensi),
                    'kemampuan_praktik' => $kemampuanPraktik,
                    'rata_rata_kemampuan_praktik' => $this->rataRata($kemampuanPraktik),
                ]
            );
        }

        if ($request->kategori === 'nilai_akhir') {
            $request->validate([
                'kehadiran' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'laporan' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'responsi' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'asistensi' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'kemampuan_praktik' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'nilai_angka' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);

            $komponen = [
                $request->filled('kehadiran') ? (float) $request->kehadiran : null,
                $request->filled('laporan') ? (float) $request->laporan : null,
                $request->filled('responsi') ? (float) $request->responsi : null,
                $request->filled('asistensi') ? (float) $request->asistensi : null,
                $request->filled('kemampuan_praktik') ? (float) $request->kemampuan_praktik : null,
            ];

            $nilaiAngka = $request->filled('nilai_angka')
                ? (float) $request->nilai_angka
                : $this->rataRata($komponen);

            NilaiAkhir::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'kehadiran' => $komponen[0],
                    'laporan' => $komponen[1],
                    'responsi' => $komponen[2],
                    'asistensi' => $komponen[3],
                    'kemampuan_praktik' => $komponen[4],
                    'nilai_angka' => $nilaiAngka,
                    'nilai_bobot' => $this->konversiBobot($nilaiAngka),
                    'nilai_huruf' => $this->konversiHuruf($nilaiAngka),
                ]
            );
        }

        return redirect()
            ->route('asisten.penilaian.show', $praktikan)
            ->with('status', 'Nilai berhasil disimpan.');
    }

    private function bersihkanNilaiArray(array $values): array
    {
        return collect($values)
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (float) $value)
            ->toArray();
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

    private function hitungRataKehadiran(array $kehadiran, int $praktikumId): ?float
    {
        $jumlahModul = Modul::where('praktikum_id', $praktikumId)->count();

        if ($jumlahModul <= 0) {
            return null;
        }

        $jumlahHadir = collect($kehadiran)
            ->filter(fn ($status) => strtolower((string) $status) === 'hadir')
            ->count();

        return round(($jumlahHadir / $jumlahModul) * 100, 2);
    }

    private function sequentialSearchPraktikan($praktikans, ?string $keyword)
    {
        if (!$keyword) {
            return $praktikans->values();
        }

        $keyword = strtolower(trim($keyword));
        $hasil = collect();

        foreach ($praktikans as $praktikan) {
            $nim = strtolower($praktikan->user->nim ?? '');
            $nama = strtolower($praktikan->user->name ?? '');

            if (
                str_contains($nim, $keyword) ||
                str_contains($nama, $keyword)
            ) {
                $hasil->push($praktikan);
            }
        }

        return $hasil->values();
    }

    private function paginateCollection($items, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();

        $items = collect($items)->values();

        $currentPageItems = $items
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        return new LengthAwarePaginator(
            $currentPageItems,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function simpanStatusPresensi(
        Asisten $asisten,
        int $praktikumId,
        Praktikan $praktikan,
        Modul $modul,
        ?string $status,
        bool $qrScanned = false
    ): void {
        $nilaiHarian = NilaiHarian::firstOrNew([
            'praktikum_id' => $praktikumId,
            'praktikan_id' => $praktikan->id,
        ]);

        $kehadiran = $nilaiHarian->kehadiran ?? [];
        $key = (string) $modul->id;

        if ($status) {
            $kehadiran[$key] = $status;
        } else {
            unset($kehadiran[$key]);
        }

        $nilaiHarian->kehadiran = $kehadiran;
        $nilaiHarian->rata_rata_kehadiran = $this->hitungRataKehadiran($kehadiran, $praktikumId);
        $nilaiHarian->save();

        if ($status) {
            Presensi::updateOrCreate(
                [
                    'praktikum_id' => $praktikumId,
                    'praktikan_id' => $praktikan->id,
                    'modul_id' => $modul->id,
                ],
                [
                    'asisten_id' => $asisten->id,
                    'waktu_absen' => now(config('app.timezone')),
                    'email' => Auth::user()->email,
                    'nim' => $praktikan->user->nim ?? null,
                    'nama' => $praktikan->user->name ?? null,
                    'kelompok' => $praktikan->kelompok->nama_kelompok ?? null,
                    'sesi' => $praktikan->kelompok->sesi ?? null,
                    'qr_code_scanned' => $qrScanned,
                    'status' => $status,
                    'keterangan' => ucfirst($status),
                ]
            );
        } else {
            Presensi::where('praktikum_id', $praktikumId)
                ->where('praktikan_id', $praktikan->id)
                ->where('modul_id', $modul->id)
                ->delete();
        }
    }

    private function konversiHuruf(?float $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        return match (true) {
            $nilai >= 85 => 'A',
            $nilai >= 80 => 'A-',
            $nilai >= 75 => 'B+',
            $nilai >= 70 => 'B',
            $nilai >= 65 => 'B-',
            $nilai >= 60 => 'C+',
            $nilai >= 55 => 'C',
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
            $nilai >= 85 => 4.00,
            $nilai >= 80 => 3.75,
            $nilai >= 75 => 3.50,
            $nilai >= 70 => 3.00,
            $nilai >= 65 => 2.75,
            $nilai >= 60 => 2.50,
            $nilai >= 55 => 2.00,
            $nilai >= 50 => 1.00,
            default => 0.00,
        };
    }
}