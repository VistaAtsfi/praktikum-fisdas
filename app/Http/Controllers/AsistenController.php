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
use App\Models\Praktikum;
use App\Models\JadwalModulPraktikum;
use App\Models\AsistenPemeriksaLaporan;
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

    private function asisten(?int $requestedPraktikumId = null): ?Asisten
    {
        $userId = Auth::id();
        if (!$userId) {
            return null;
        }

        $praktikumId = $requestedPraktikumId
            ?? request('praktikum_id')
            ?? session('asisten_praktikum_id');

        if ($praktikumId) {
            $asisten = Asisten::with('user', 'praktikum')
                ->where('user_id', $userId)
                ->where('praktikum_id', $praktikumId)
                ->first();

            if (!$asisten) {
                $praktikum = Praktikum::find($praktikumId);
                if ($praktikum) {
                    $asisten = Asisten::firstOrCreate([
                        'user_id' => $userId,
                        'praktikum_id' => $praktikum->id,
                    ], [
                        'status' => 'aktif',
                    ]);
                    $asisten->load('user', 'praktikum');
                }
            }

            if ($asisten) {
                session(['asisten_praktikum_id' => $asisten->praktikum_id]);
                return $asisten;
            }
        }

        $asisten = Asisten::with('user', 'praktikum')
            ->where('user_id', $userId)
            ->latest('praktikum_id')
            ->first();

        if ($asisten) {
            session(['asisten_praktikum_id' => $asisten->praktikum_id]);
        }

        return $asisten;
    }

    public function switchPraktikum(Request $request): RedirectResponse
    {
        $this->pastikanAsisten();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
        ]);

        session(['asisten_praktikum_id' => (int) $request->praktikum_id]);

        return back()->with('status', 'Berhasil beralih praktikum.');
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
                ->has('praktikans')
                ->get()
                ->sort(function ($a, $b) {
                    $prodiCmp = strcasecmp((string) $a->prodi, (string) $b->prodi);
                    if ($prodiCmp !== 0) {
                        return $prodiCmp;
                    }
                    return strnatcasecmp((string) $a->nama_kelompok, (string) $b->nama_kelompok);
                })
                ->values()
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

        $moduls = $moduls->values();

        $praktikanBelumAsistensi = $praktikans
            ->map(function ($praktikan) use ($moduls) {
                $asistensi = collect($praktikan->nilaiHarian?->asistensi ?? []);

                $modulBelumAsistensi = $moduls
                    ->filter(function ($modul) use ($asistensi) {
                        $nilai = $asistensi->get((string) $modul->id);

                        return $nilai === null || $nilai === '' || !is_numeric($nilai);
                    })
                    ->map(function ($modul, $index) use ($moduls) {
                        $nomorPertemuan = $moduls->search(fn ($item) => $item->id === $modul->id) + 1;

                        return [
                            'pertemuan' => $nomorPertemuan,
                            'nama_modul' => $modul->nama_modul,
                        ];
                    })
                    ->values();

                $praktikan->modul_belum_asistensi = $modulBelumAsistensi;

                return $praktikan;
            })
            ->filter(function ($praktikan) {
                return $praktikan->modul_belum_asistensi->isNotEmpty();
            })
            ->values();

        $praktikanBelumAsistensiByProdi = $praktikanBelumAsistensi
            ->groupBy(fn ($p) => $p->user?->prodi ?: 'Lainnya')
            ->sortKeys();

        return view('asisten.beranda', compact(
            'asisten',
            'praktikum',
            'praktikans',
            'kelompoks',
            'moduls',
            'jumlahMateri',
            'jumlahPresensi',
            'praktikanBelumAsistensi',
            'praktikanBelumAsistensiByProdi'
        ));
    }

    public function jadwal(Request $request)
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        if (!$praktikum) {

            $jadwals = collect();
            $kelompoks = collect();
            $moduls = collect();

            return view('asisten.jadwal', compact(
                'asisten',
                'praktikum',
                'jadwals',
                'kelompoks',
                'moduls'
            ));
        }

        $kelompoks = Kelompok::where('praktikum_id', $praktikum->id)
            ->has('praktikans')
            ->get()
            ->sort(function ($a, $b) {
                $prodiCmp = strcasecmp((string) $a->prodi, (string) $b->prodi);
                if ($prodiCmp !== 0) {
                    return $prodiCmp;
                }
                return strnatcasecmp((string) $a->nama_kelompok, (string) $b->nama_kelompok);
            })
            ->values();

        $moduls = Modul::where('praktikum_id', $praktikum->id)
            ->orderBy('id')
            ->get();

        $prodis = JadwalModulPraktikum::where('praktikum_id', $praktikum->id)
            ->whereNotNull('prodi')
            ->where('prodi', '!=', '')
            ->pluck('prodi')
            ->unique()
            ->sort()
            ->values();

        $query = JadwalModulPraktikum::with([
                'kelompok',
                'modul',
            ])
            ->where('praktikum_id', $praktikum->id);

        $penugasans = AsistenPemeriksaLaporan::where('asisten_id', $asisten->id)
            ->where('praktikum_id', $praktikum->id)
            ->get();

        $query->where(function ($q) use ($penugasans) {

            foreach ($penugasans as $penugasan) {

                $q->orWhere(function ($sub) use ($penugasan) {

                    $sub->where('kelompok_id', $penugasan->kelompok_id)
                        ->where('modul_id', $penugasan->modul_id)
                        ->where('prodi', $penugasan->prodi);

                });

            }

        });

        // if ($penugasans->isEmpty()) {
        //     $query->whereRaw('1 = 0');
        // }

        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }

        if ($request->filled('modul_id')) {
            $query->where('modul_id', $request->modul_id);
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('prodi', 'like', "%{$search}%")
                ->orWhere('keterangan', 'like', "%{$search}%")
                ->orWhere('kelompok_label', 'like', "%{$search}%")
                ->orWhereHas('kelompok', function ($qq) use ($search) {

                        $qq->where('nama_kelompok', 'like', "%{$search}%");

                })
                ->orWhereHas('modul', function ($qq) use ($search) {

                        $qq->where('nama_modul', 'like', "%{$search}%");

                });

            });
        }

        $jadwals = $query
            ->orderBy('minggu_ke')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate(10)
            ->withQueryString();

        return view('asisten.jadwal', compact(
            'asisten',
            'praktikum',
            'jadwals',
            'kelompoks',
            'moduls',
            'prodis'
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

        $semuaKelompoks = $praktikum
            ? Kelompok::where('praktikum_id', $praktikum->id)
                ->has('praktikans')
                ->get()
                ->sort(function ($a, $b) {
                    $prodiCmp = strcasecmp((string) $a->prodi, (string) $b->prodi);
                    if ($prodiCmp !== 0) {
                        return $prodiCmp;
                    }
                    return strnatcasecmp((string) $a->nama_kelompok, (string) $b->nama_kelompok);
                })
                ->values()
            : collect();

        $kelompoks = $semuaKelompoks
            ->filter(fn ($kelompok) => !empty($kelompok->nama_kelompok))
            ->unique('nama_kelompok')
            ->sortBy('nama_kelompok', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        $prodis = $praktikum
            ? Praktikan::where('praktikum_id', $praktikum->id)
                ->whereHas('user')
                ->with('user')
                ->get()
                ->pluck('user.prodi')
                ->filter()
                ->unique()
                ->sort()
                ->values()
            : collect();

        $sesis = $semuaKelompoks
            ->pluck('sesi')
            ->filter()
            ->unique()
            ->sort(SORT_NATURAL)
            ->values();

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

        $query = Praktikan::with([
            'user',
            'kelompok',
            'nilaiHarian',
            'nilaiAkhir',
        ])
            ->where('praktikum_id', $praktikum->id);

        if ($request->filled('kelompok')) {
            $query->whereHas('kelompok', function ($q) use ($request) {
                $q->where('nama_kelompok', $request->kelompok);
            });
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

        $praktikans = $this->paginateCollection(
            $hasilSearch,
            $request,
            10
        );

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

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        abort_if(!$asisten || !$praktikum, 403);

        // Jika form mengirimkan batch presensi sekaligus
        if ($request->has('presensi') && is_array($request->presensi)) {
            $praktikanIds = array_keys($request->presensi);
            $praktikans = Praktikan::with(['user', 'kelompok'])
                ->where('praktikum_id', $praktikum->id)
                ->whereIn('id', $praktikanIds)
                ->get()
                ->keyBy('id');

            $moduls = Modul::where('praktikum_id', $praktikum->id)
                ->get()
                ->keyBy('id');

            foreach ($request->presensi as $pId => $modulStatuses) {
                $praktikan = $praktikans->get($pId);
                if (!$praktikan || !is_array($modulStatuses)) {
                    continue;
                }

                foreach ($modulStatuses as $mId => $status) {
                    $modul = $moduls->get($mId);
                    if (!$modul) {
                        continue;
                    }

                    if (!in_array($status, ['kosong', 'hadir', 'sakit', 'izin', 'alpa', '', null])) {
                        continue;
                    }

                    $statusVal = ($status === 'kosong' || empty($status)) ? null : $status;

                    $this->simpanStatusPresensi(
                        asisten: $asisten,
                        praktikumId: $praktikum->id,
                        praktikan: $praktikan,
                        modul: $modul,
                        status: $statusVal,
                        qrScanned: false
                    );
                }
            }

            return back()->with('status', 'Semua perubahan presensi berhasil disimpan.');
        }

        // Fallback untuk single update
        $request->validate([
            'praktikan_id' => ['required', 'exists:praktikans,id'],
            'modul_id' => ['required', 'exists:moduls,id'],
            'status' => ['nullable', 'in:kosong,hadir,sakit,izin,alpa'],
        ]);

        $praktikan = Praktikan::with(['user', 'kelompok'])
            ->where('praktikum_id', $praktikum->id)
            ->findOrFail($request->praktikan_id);

        $modul = Modul::where('praktikum_id', $praktikum->id)
            ->findOrFail($request->modul_id);

        $status = ($request->status === 'kosong' || empty($request->status)) ? null : $request->status;

        $this->simpanStatusPresensi(
            asisten: $asisten,
            praktikumId: $praktikum->id,
            praktikan: $praktikan,
            modul: $modul,
            status: $status,
            qrScanned: false
        );

        return back()->with('status', 'Status presensi berhasil disimpan.');
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

        $materis = $query->paginate(10)->withQueryString();

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

        $downloadUrl = $this->getDirectDownloadUrl($materi->link);

        return redirect()->away($downloadUrl);
    }

    private function getDirectDownloadUrl(string $link): string
    {
        $link = trim($link);

        if (preg_match('/(?:drive\.google\.com\/file\/d\/|docs\.google\.com\/[^\/]+\/d\/)([a-zA-Z0-9_-]+)/', $link, $matches)) {
            return 'https://drive.google.com/uc?export=download&id=' . $matches[1];
        }

        if (preg_match('/drive\.google\.com\/[^\?]*\?[^#]*\bid=([a-zA-Z0-9_-]+)/', $link, $matches)) {
            return 'https://drive.google.com/uc?export=download&id=' . $matches[1];
        }

        return $link;
    }

    public function penilaian(Request $request)
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        $kelompoks = $praktikum
            ? Kelompok::where('praktikum_id', $praktikum->id)
                ->has('praktikans')
                ->get()
                ->sort(function ($a, $b) {
                    $prodiCmp = strcasecmp((string) $a->prodi, (string) $b->prodi);
                    if ($prodiCmp !== 0) {
                        return $prodiCmp;
                    }
                    return strnatcasecmp((string) $a->nama_kelompok, (string) $b->nama_kelompok);
                })
                ->values()
            : collect();

        $prodis = $praktikum
            ? Praktikan::where('praktikum_id', $praktikum->id)
                ->whereHas('user')
                ->with('user')
                ->get()
                ->pluck('user.prodi')
                ->filter()
                ->unique()
                ->sort()
                ->values()
            : collect();

        if (!$praktikum) {
            $praktikans = collect();
            $rekapNilaiHuruf = collect();
            $chartNilaiLabels = ['A', 'B', 'C', 'D', 'E'];
            $chartNilaiData = [0, 0, 0, 0, 0];
            $totalDinilai = 0;
            $totalBelumDinilai = 0;
            $totalPraktikanChart = 0;

            return view('asisten.penilaian', compact(
                'asisten',
                'praktikum',
                'praktikans',
                'kelompoks',
                'prodis',
                'rekapNilaiHuruf',
                'chartNilaiLabels',
                'chartNilaiData',
                'totalDinilai',
                'totalBelumDinilai',
                'totalPraktikanChart'
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

        if ($request->filled('prodi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('prodi', $request->prodi);
            });
        }

        if ($request->filled('kelompok')) {
            $query->whereHas('kelompok', function ($q) use ($request) {
                $q->where('nama_kelompok', $request->kelompok);
            });
        }

        if ($request->filled('status_nilai')) {
            if ($request->status_nilai === 'sudah') {
                $query->whereHas('nilaiAkhir');
            }

            if ($request->status_nilai === 'belum') {
                $query->doesntHave('nilaiAkhir');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Data untuk chart
        |--------------------------------------------------------------------------
        | Chart mengikuti filter yang sedang dipakai di halaman penilaian.
        */
        $praktikansUntukChart = (clone $query)
            ->with(['user', 'kelompok', 'nilaiAkhir'])
            ->get();

        $chartNilaiLabels = ['A', 'B', 'C', 'D', 'E'];

        $rekapNilaiHuruf = collect($chartNilaiLabels)->mapWithKeys(function ($huruf) use ($praktikansUntukChart) {
            $items = $praktikansUntukChart
                ->filter(function ($praktikan) use ($huruf) {
                    return strtoupper((string) ($praktikan->nilaiAkhir?->nilai_huruf ?? '')) === $huruf;
                })
                ->values();

            return [
                $huruf => [
                    'total' => $items->count(),
                    'praktikans' => $items->map(function ($praktikan) {
                        return [
                            'nim' => $praktikan->user->nim ?? '-',
                            'nama' => $praktikan->user->name ?? '-',
                            'prodi' => $praktikan->user->prodi ?? '-',
                            'kelompok' => $praktikan->kelompok->nama_kelompok ?? '-',
                            'nilai_angka' => $praktikan->nilaiAkhir?->nilai_angka ?? null,
                        ];
                    })->values(),
                ],
            ];
        });

        $chartNilaiData = collect($chartNilaiLabels)
            ->map(fn ($huruf) => $rekapNilaiHuruf[$huruf]['total'] ?? 0)
            ->values()
            ->toArray();

        $totalDinilai = $praktikansUntukChart
            ->filter(fn ($praktikan) => !empty($praktikan->nilaiAkhir?->nilai_huruf))
            ->count();

        $totalBelumDinilai = $praktikansUntukChart
            ->filter(fn ($praktikan) => empty($praktikan->nilaiAkhir?->nilai_huruf))
            ->count();

        $totalPraktikanChart = $praktikansUntukChart->count();

        $praktikans = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('asisten.penilaian', compact(
            'asisten',
            'praktikum',
            'praktikans',
            'kelompoks',
            'prodis',
            'rekapNilaiHuruf',
            'chartNilaiLabels',
            'chartNilaiData',
            'totalDinilai',
            'totalBelumDinilai',
            'totalPraktikanChart'
        ));
    }

    public function beriNilai(Praktikan $praktikan)
    {
        $this->pastikanAsisten();

        $asisten = $this->asisten();
        $praktikum = $asisten?->praktikum;

        abort_if(!$asisten || !$praktikum, 403);
        abort_if($praktikan->praktikum_id !== $praktikum->id, 403);

        $this->sinkronPenulisanLaporan($praktikum->id, $praktikan->id);
        $this->sinkronNilaiAkhir($praktikum->id, $praktikan->id);

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
            'kategori' => ['required', 'in:penulisan_laporan,laporan_resmi,kemampuan_praktik,nilai_harian'],
        ]);

        if ($request->kategori === 'penulisan_laporan') {
            $request->validate([
                'laporan_mingguan' => ['nullable', 'array'],
                'laporan_mingguan.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            ]);

            $laporanMingguan = $this->bersihkanNilaiArray($request->input('laporan_mingguan', []));
            $rataMingguan = $this->rataRata($laporanMingguan);

            $rataResmi = NilaiLaporanResmi::where('praktikum_id', $praktikum->id)
                ->where('praktikan_id', $praktikan->id)
                ->value('rata_rata');

            $nilaiAkhirLaporan = $this->rataRata([$rataMingguan, $rataResmi]);

            NilaiPenulisanLaporan::updateOrCreate(
                [
                    'praktikum_id' => $praktikum->id,
                    'praktikan_id' => $praktikan->id,
                ],
                [
                    'laporan_mingguan' => $laporanMingguan,
                    'rata_rata_mingguan' => $rataMingguan,
                    'rata_rata_resmi' => $rataResmi,
                    'nilai_akhir' => $nilaiAkhirLaporan,
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

            $this->sinkronPenulisanLaporan($praktikum->id, $praktikan->id);
        }

        if ($request->kategori === 'kemampuan_praktik') {
            $request->validate([
                'nilai_modul' => ['nullable', 'array'],
                'nilai_modul.*.k1' => ['nullable', 'numeric', 'min:0', 'max:3'],
                'nilai_modul.*.k2' => ['nullable', 'numeric', 'min:0', 'max:3'],
                'nilai_modul.*.k3' => ['nullable', 'numeric', 'min:0', 'max:3'],
                'nilai_modul.*.k4' => ['nullable', 'numeric', 'min:0', 'max:3'],
            ]);

            $nilaiModul = $this->bersihkanNilaiKemampuanPraktikum($request->input('nilai_modul', []));
            $rataRata = $this->rataRataKemampuanPraktikum($nilaiModul);

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

            $nilaiHarian = NilaiHarian::firstOrNew([
                'praktikum_id' => $praktikum->id,
                'praktikan_id' => $praktikan->id,
            ]);

            $nilaiHarian->kemampuan_praktik = collect($nilaiModul)
                ->mapWithKeys(fn ($item, $modulId) => [$modulId => $item['rerata'] ?? null])
                ->filter(fn ($value) => $value !== null)
                ->toArray();

            $nilaiHarian->rata_rata_kemampuan_praktik = $rataRata;
            $nilaiHarian->save();
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

        $this->sinkronPenulisanLaporan($praktikum->id, $praktikan->id);
        $this->sinkronNilaiAkhir($praktikum->id, $praktikan->id);

        return redirect()
            ->route('asisten.penilaian.show', $praktikan)
            ->with('status', 'Nilai berhasil disimpan dan nilai akhir diperbarui otomatis.');
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

        $presensiLama = Presensi::where('praktikum_id', $praktikumId)
            ->where('praktikan_id', $praktikan->id)
            ->where('modul_id', $modul->id)
            ->first();

        if ($status) {
            $statusLama = $presensiLama?->status;
            $pernahScanQr = (bool) ($presensiLama?->qr_code_scanned);

            $qrCodeScanned = $qrScanned || $pernahScanQr;

            if ($qrScanned) {
                $keterangan = 'Presensi disimpan melalui scan QR dengan status ' . ucfirst($status) . '.';
            } elseif ($pernahScanQr && $statusLama && $statusLama !== $status) {
                $keterangan = 'Status kehadiran diubah manual dari ' . ucfirst($statusLama) . ' menjadi ' . ucfirst($status) . '.';
            } elseif ($pernahScanQr) {
                $keterangan = 'Presensi hasil scan QR diperbarui manual dengan status ' . ucfirst($status) . '.';
            } else {
                $keterangan = 'Presensi diinput manual dengan status ' . ucfirst($status) . '.';
            }

            Presensi::updateOrCreate(
                [
                    'praktikum_id' => $praktikumId,
                    'praktikan_id' => $praktikan->id,
                    'modul_id' => $modul->id,
                ],
                [
                    'asisten_id' => $asisten->id,
                    'waktu_absen' => $qrScanned
                        ? now(config('app.timezone'))
                        : ($presensiLama?->waktu_absen ?? now(config('app.timezone'))),
                    'email' => $qrScanned
                        ? Auth::user()->email
                        : ($presensiLama?->email ?? Auth::user()->email),
                    'nim' => $praktikan->user->nim ?? null,
                    'nama' => $praktikan->user->name ?? null,
                    'kelompok' => $praktikan->kelompok->nama_kelompok ?? null,
                    'sesi' => $praktikan->kelompok->sesi ?? null,
                    'qr_code_scanned' => $qrCodeScanned,
                    'status' => $status,
                    'keterangan' => $keterangan,
                ]
            );
        } else {
            if ($presensiLama && $presensiLama->qr_code_scanned) {
                $presensiLama->update([
                    'status' => null,
                    'keterangan' => 'Status kehadiran hasil scan QR dikosongkan melalui perubahan manual.',
                ]);
            } else {
                Presensi::where('praktikum_id', $praktikumId)
                    ->where('praktikan_id', $praktikan->id)
                    ->where('modul_id', $modul->id)
                    ->delete();
            }
        }

        $this->sinkronNilaiAkhir($praktikumId, $praktikan->id);
    }

    private function normalisasiAngka($value): ?float
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }

    private function bersihkanNilaiKemampuanPraktikum(array $values): array
    {
        $result = [];

        foreach ($values as $modulId => $item) {
            $k1 = $this->normalisasiAngka($item['k1'] ?? null);
            $k2 = $this->normalisasiAngka($item['k2'] ?? null);
            $k3 = $this->normalisasiAngka($item['k3'] ?? null);
            $k4 = $this->normalisasiAngka($item['k4'] ?? null);

            $adaNilai = collect([$k1, $k2, $k3, $k4])
                ->filter(fn ($value) => $value !== null)
                ->isNotEmpty();

            if (!$adaNilai) {
                continue;
            }

            $result[$modulId] = [
                'k1' => $k1,
                'k2' => $k2,
                'k3' => $k3,
                'k4' => $k4,
                'rerata' => $this->hitungNilaiKemampuanModul($k1, $k2, $k3, $k4),
            ];
        }

        return $result;
    }

    private function hitungNilaiKemampuanModul(?float $k1, ?float $k2, ?float $k3, ?float $k4): ?float
    {
        $adaNilai = collect([$k1, $k2, $k3, $k4])
            ->filter(fn ($value) => $value !== null)
            ->isNotEmpty();

        if (!$adaNilai) {
            return null;
        }

        $nilai = (
            (($k1 ?? 0) * 0.10 / 3) +
            (($k2 ?? 0) * 0.20 / 3) +
            (($k3 ?? 0) * 0.20 / 3) +
            (($k4 ?? 0) * 0.50 / 3)
        ) * 90;

        return round($nilai, 2);
    }

    private function rataRataKemampuanPraktikum(array $nilaiModul): ?float
    {
        $values = collect($nilaiModul)
            ->pluck('rerata')
            ->filter(fn ($value) => $value !== null && $value !== '' && is_numeric($value))
            ->map(fn ($value) => (float) $value);

        if ($values->isEmpty()) {
            return null;
        }

        return round($values->avg(), 2);
    }

    private function sinkronPenulisanLaporan(int $praktikumId, int $praktikanId): void
    {
        $nilaiPenulisan = NilaiPenulisanLaporan::firstOrNew([
            'praktikum_id' => $praktikumId,
            'praktikan_id' => $praktikanId,
        ]);

        $laporanMingguan = $nilaiPenulisan->laporan_mingguan ?? [];
        $rataMingguan = $this->rataRata($laporanMingguan);

        $rataResmi = NilaiLaporanResmi::where('praktikum_id', $praktikumId)
            ->where('praktikan_id', $praktikanId)
            ->value('rata_rata');

        $nilaiPenulisan->rata_rata_mingguan = $rataMingguan;
        $nilaiPenulisan->rata_rata_resmi = $rataResmi;
        $nilaiPenulisan->nilai_akhir = $this->rataRata([$rataMingguan, $rataResmi]);
        $nilaiPenulisan->save();
    }

    private function sinkronNilaiAkhir(int $praktikumId, int $praktikanId): void
    {
        $nilaiHarian = NilaiHarian::where('praktikum_id', $praktikumId)
            ->where('praktikan_id', $praktikanId)
            ->first();

        $nilaiPenulisan = NilaiPenulisanLaporan::where('praktikum_id', $praktikumId)
            ->where('praktikan_id', $praktikanId)
            ->first();

        $nilaiKemampuan = NilaiKemampuanPraktikum::where('praktikum_id', $praktikumId)
            ->where('praktikan_id', $praktikanId)
            ->first();

        $kehadiran = $this->normalisasiAngka($nilaiHarian?->rata_rata_kehadiran);
        $laporan = $this->normalisasiAngka($nilaiPenulisan?->nilai_akhir);
        $responsi = $this->normalisasiAngka($nilaiHarian?->rata_rata_responsi);
        $asistensi = $this->normalisasiAngka($nilaiHarian?->rata_rata_asistensi);
        $kemampuanPraktik = $this->normalisasiAngka(
            $nilaiHarian?->rata_rata_kemampuan_praktik ?? $nilaiKemampuan?->rata_rata_akhir
        );

        $nilaiAngka = $this->hitungNilaiAngkaAkhir(
            $kehadiran,
            $laporan,
            $responsi,
            $asistensi,
            $kemampuanPraktik
        );

        NilaiAkhir::updateOrCreate(
            [
                'praktikum_id' => $praktikumId,
                'praktikan_id' => $praktikanId,
            ],
            [
                'kehadiran' => $kehadiran,
                'laporan' => $laporan,
                'responsi' => $responsi,
                'asistensi' => $asistensi,
                'kemampuan_praktik' => $kemampuanPraktik,
                'nilai_angka' => $nilaiAngka,
                'nilai_bobot' => $this->konversiBobot($nilaiAngka),
                'nilai_huruf' => $this->konversiHuruf($nilaiAngka),
            ]
        );
    }

    private function hitungNilaiAngkaAkhir(
        ?float $kehadiran,
        ?float $laporan,
        ?float $responsi,
        ?float $asistensi,
        ?float $kemampuanPraktik
    ): ?float {
        $adaNilai = collect([
            $kehadiran,
            $laporan,
            $responsi,
            $asistensi,
            $kemampuanPraktik,
        ])->filter(fn ($value) => $value !== null)->isNotEmpty();

        if (!$adaNilai) {
            return null;
        }

        $nilai = (($kehadiran ?? 0) * 0.10)
            + (($laporan ?? 0) * 0.10)
            + (($responsi ?? 0) * 0.10)
            + (($asistensi ?? 0) * 0.20)
            + (($kemampuanPraktik ?? 0) * 0.50);

        return round($nilai, 2);
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
}