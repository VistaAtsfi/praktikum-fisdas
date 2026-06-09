<?php

namespace App\Http\Controllers;

use App\Models\Asisten;
use App\Models\Kelompok;
use App\Models\LogSearch;
use App\Models\LogSorting;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\NilaiAkhir;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\PraktikumProdi;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KoordinatorController extends Controller
{
    private function pastikanKoordinator(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'koordinator', 403);
    }

    private function praktikumAktif(): ?Praktikum
    {
        return Praktikum::where('is_aktif', true)
            ->latest()
            ->first()
            ?? Praktikum::latest()->first();
    }

    public function beranda(): View
    {
        $this->pastikanKoordinator();

        $praktikumAktif = $this->praktikumAktif();

        $totalPeriode = Praktikum::count();

        $totalPraktikan = $praktikumAktif
            ? Praktikan::where('praktikum_id', $praktikumAktif->id)->count()
            : 0;

        $totalAsisten = $praktikumAktif
            ? Asisten::where('praktikum_id', $praktikumAktif->id)->where('status', 'aktif')->count()
            : 0;

        $totalKelompok = $praktikumAktif
            ? Kelompok::where('praktikum_id', $praktikumAktif->id)->count()
            : 0;

        $nilaiAkhirs = $praktikumAktif
            ? NilaiAkhir::where('praktikum_id', $praktikumAktif->id)->get()
            : collect();

        $totalDinilai = $nilaiAkhirs->filter(fn ($nilai) => $nilai->nilai_angka !== null)->count();

        $totalLulus = $nilaiAkhirs
            ->filter(fn ($nilai) => $nilai->nilai_angka !== null && (float) $nilai->nilai_angka >= 60)
            ->count();

        $persentaseLulus = $totalDinilai > 0
            ? round(($totalLulus / $totalDinilai) * 100, 2)
            : 0;

        $chartKelulusanLabels = ['Lulus', 'Belum/Tidak Lulus'];
        $chartKelulusanData = [
            $totalLulus,
            max(0, $totalDinilai - $totalLulus),
        ];

        return view('koordinator.beranda', compact(
            'praktikumAktif',
            'totalPeriode',
            'totalPraktikan',
            'totalAsisten',
            'totalKelompok',
            'totalDinilai',
            'totalLulus',
            'persentaseLulus',
            'chartKelulusanLabels',
            'chartKelulusanData'
        ));
    }

    public function periode(Request $request): View
    {
        $this->pastikanKoordinator();

        $tahunAkademikOptions = Praktikum::whereNotNull('tahun_akademik')
            ->select('tahun_akademik')
            ->distinct()
            ->orderByDesc('tahun_akademik')
            ->pluck('tahun_akademik');

        $query = Praktikum::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('jenis_praktikum', 'like', "%{$search}%")
                    ->orWhere('tahun_akademik', 'like', "%{$search}%")
                    ->orWhere('semester', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tahun_akademik')) {
            $query->where('tahun_akademik', $request->tahun_akademik);
        }

        $praktikums = $query
            ->orderByDesc('is_aktif')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $praktikumProdis = PraktikumProdi::with('praktikum')
            ->orderBy('nama_prodi')
            ->get()
            ->groupBy('praktikum_id');

        return view('koordinator.periode', compact(
            'praktikums',
            'tahunAkademikOptions',
            'praktikumProdis'
        ));
    }

    public function storeProdiPraktikum(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'nama_prodi' => [
                'required',
                'string',
                'max:100',
                Rule::unique('praktikum_prodis', 'nama_prodi')
                    ->where(fn ($query) => $query->where('praktikum_id', $request->praktikum_id)),
            ],
        ], [
            'nama_prodi.unique' => 'Program studi ini sudah terdaftar pada periode praktikum tersebut.',
        ]);

        PraktikumProdi::create([
            'praktikum_id' => $request->praktikum_id,
            'nama_prodi' => trim($request->nama_prodi),
        ]);

        return back()->with('status', 'Program studi berhasil ditambahkan ke periode praktikum.');
    }

    public function deleteProdiPraktikum(PraktikumProdi $praktikumProdi): RedirectResponse
    {
        $this->pastikanKoordinator();

        $praktikumProdi->delete();

        return back()->with('status', 'Program studi berhasil dihapus dari periode praktikum.');
    }

    public function updatePeriode(Request $request, Praktikum $praktikum): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'nama' => ['required', 'in:Praktikum Fisika Dasar 1,Praktikum Fisika Dasar 2'],
            'jenis_praktikum' => ['required', 'in:fisdas_1,fisdas_2'],
            'tahun_akademik' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'is_aktif' => ['nullable', 'boolean'],
        ], [
            'tahun_akademik.regex' => 'Format tahun akademik harus menggunakan slash, contoh: 2025/2026.',
        ]);

        $jenisSeharusnya = match ($request->nama) {
            'Praktikum Fisika Dasar 1' => 'fisdas_1',
            'Praktikum Fisika Dasar 2' => 'fisdas_2',
            default => null,
        };

        if ($request->jenis_praktikum !== $jenisSeharusnya) {
            return back()
                ->withInput()
                ->withErrors([
                    'jenis_praktikum' => 'Jenis praktikum harus sesuai dengan nama praktikum yang dipilih.',
                ]);
        }

        if ($request->boolean('is_aktif')) {
            Praktikum::where('id', '!=', $praktikum->id)->update([
                'is_aktif' => false,
            ]);
        }

        $praktikum->update([
            'nama' => $request->nama,
            'jenis_praktikum' => $request->jenis_praktikum,
            'tahun_akademik' => $request->tahun_akademik,
            'semester' => $request->semester,
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return back()->with('status', 'Periode praktikum berhasil diperbarui.');
    }

    public function periodeDetail(Praktikum $praktikum): View
    {
        $this->pastikanKoordinator();

        $praktikanList = Praktikan::with(['user', 'kelompok'])
            ->where('praktikum_id', $praktikum->id)
            ->get();

        $asistenList = Asisten::with('user')
            ->where('praktikum_id', $praktikum->id)
            ->get();

        $jumlahProgramStudi = $praktikanList
            ->pluck('user.prodi')
            ->filter()
            ->unique()
            ->count();

        $jumlahPraktikan = $praktikanList->count();

        $praktikanLaki = $praktikanList
            ->filter(fn ($praktikan) => $praktikan->user?->jenis_kelamin === 'L')
            ->count();

        $praktikanPerempuan = $praktikanList
            ->filter(fn ($praktikan) => $praktikan->user?->jenis_kelamin === 'P')
            ->count();

        $jumlahAsisten = $asistenList->count();

        $asistenLaki = $asistenList
            ->filter(fn ($asisten) => $asisten->user?->jenis_kelamin === 'L')
            ->count();

        $asistenPerempuan = $asistenList
            ->filter(fn ($asisten) => $asisten->user?->jenis_kelamin === 'P')
            ->count();

        $totalKelompok = Kelompok::where('praktikum_id', $praktikum->id)->count();

        /*
        |--------------------------------------------------------------------------
        | Total kelompok per prodi
        |--------------------------------------------------------------------------
        | Karena tabel kelompoks belum punya kolom prodi, jumlah kelompok per prodi
        | dihitung dari prodi praktikan yang sudah masuk ke kelompok.
        */
        $kelompokPerProdi = $praktikanList
            ->filter(fn ($praktikan) => $praktikan->user?->prodi && $praktikan->kelompok_id)
            ->groupBy(fn ($praktikan) => $praktikan->user->prodi)
            ->map(fn ($items) => $items->pluck('kelompok_id')->unique()->count())
            ->sortKeys();

        $totalPertemuan = Modul::where('praktikum_id', $praktikum->id)->count();

        $pertemuanBerjalan = Presensi::where('praktikum_id', $praktikum->id)
            ->whereNotNull('modul_id')
            ->distinct()
            ->count('modul_id');

        $rataNilaiAkhir = round((float) (NilaiAkhir::where('praktikum_id', $praktikum->id)->avg('nilai_angka') ?? 0), 2);

        $chartPraktikanGenderLabels = ['Laki-laki', 'Perempuan'];
        $chartPraktikanGenderData = [$praktikanLaki, $praktikanPerempuan];

        $chartAsistenGenderLabels = ['Laki-laki', 'Perempuan'];
        $chartAsistenGenderData = [$asistenLaki, $asistenPerempuan];

        $chartKelompokProdiLabels = $kelompokPerProdi->keys()->values();
        $chartKelompokProdiData = $kelompokPerProdi->values();

        $chartNilaiAkhirLabels = ['Rata-rata Nilai', 'Sisa ke 100'];
        $chartNilaiAkhirData = [
            $rataNilaiAkhir,
            max(0, 100 - $rataNilaiAkhir),
        ];

        return view('koordinator.periode-detail', compact(
            'praktikum',
            'jumlahProgramStudi',
            'jumlahPraktikan',
            'praktikanLaki',
            'praktikanPerempuan',
            'jumlahAsisten',
            'asistenLaki',
            'asistenPerempuan',
            'totalKelompok',
            'kelompokPerProdi',
            'totalPertemuan',
            'pertemuanBerjalan',
            'rataNilaiAkhir',
            'chartPraktikanGenderLabels',
            'chartPraktikanGenderData',
            'chartAsistenGenderLabels',
            'chartAsistenGenderData',
            'chartKelompokProdiLabels',
            'chartKelompokProdiData',
            'chartNilaiAkhirLabels',
            'chartNilaiAkhirData'
        ));
    }

    public function materi(Request $request): View
    {
        $this->pastikanKoordinator();

        $praktikums = Praktikum::latest()->get();
        $praktikumId = $request->praktikum_id ?? $this->praktikumAktif()?->id;

        $moduls = $praktikumId
            ? Modul::where('praktikum_id', $praktikumId)->orderBy('id')->get()
            : collect();

        $query = Materi::with(['praktikum', 'modul']);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('modul', fn ($m) => $m->where('nama_modul', 'like', "%{$search}%"));
            });
        }

        $materis = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('koordinator.materi', compact(
            'praktikums',
            'praktikumId',
            'moduls',
            'materis'
        ));
    }

    public function storeMateri(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'nama_modul' => ['nullable', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'link' => ['nullable', 'string', 'max:500'],
        ]);

        $modulId = null;

        if ($request->filled('nama_modul')) {
            $modul = Modul::firstOrCreate(
                [
                    'praktikum_id' => $request->praktikum_id,
                    'nama_modul' => trim($request->nama_modul),
                ]
            );

            $modulId = $modul->id;
        }

        Materi::create([
            'praktikum_id' => $request->praktikum_id,
            'modul_id' => $modulId,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'link' => $request->link,
        ]);

        return back()->with('status', 'Materi berhasil ditambahkan.');
    }

    public function deleteMateri(Materi $materi): RedirectResponse
    {
        $this->pastikanKoordinator();

        $materi->delete();

        return back()->with('status', 'Materi berhasil dihapus.');
    }

    public function asisten(Request $request): View
    {
        $this->pastikanKoordinator();

        $praktikums = Praktikum::latest()->get();

        $prodisByPraktikum = PraktikumProdi::orderBy('nama_prodi')
            ->get()
            ->groupBy('praktikum_id')
            ->map(fn ($items) => $items->pluck('nama_prodi')->values())
            ->toArray();

        $query = Asisten::with(['user', 'praktikum']);

        if ($request->filled('praktikum_id')) {
            $query->where('praktikum_id', $request->praktikum_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('prodi', 'like', "%{$search}%");
            });
        }

        $asistens = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('koordinator.asisten', compact(
            'praktikums',
            'asistens',
            'prodisByPraktikum'
        ));
    }

    public function storeAsisten(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nim' => ['nullable', 'string', 'max:30', 'unique:users,nim'],
            'prodi' => [
                'required',
                'string',
                'max:100',
                Rule::exists('praktikum_prodis', 'nama_prodi')
                    ->where(fn ($query) => $query->where('praktikum_id', $request->praktikum_id)),
            ],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'nomor_ponsel' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ], [
            'prodi.exists' => 'Program studi belum terdaftar pada periode praktikum yang dipilih.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'role' => 'asisten',
            'nim' => $request->nim,
            'prodi' => $request->prodi,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nomor_ponsel' => $request->nomor_ponsel,
            'profile_completed' => true,
        ]);

        Asisten::create([
            'user_id' => $user->id,
            'praktikum_id' => $request->praktikum_id,
            'status' => $request->status,
        ]);

        return back()->with('status', 'Akun asisten berhasil dibuat.');
    }

    public function resetPasswordAsisten(Request $request, Asisten $asisten): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $asisten->user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password asisten berhasil direset.');
    }

    public function praktikan(Request $request): View
    {
        $this->pastikanKoordinator();

        $praktikums = Praktikum::latest()->get();
        $praktikumId = $request->praktikum_id ?? $this->praktikumAktif()?->id;

        $prodis = User::where('role', 'praktikan')
            ->whereNotNull('prodi')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');

        $query = Praktikan::with(['user', 'praktikum', 'kelompok']);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        if ($request->filled('prodi')) {
            $query->whereHas('user', fn ($q) => $q->where('prodi', $request->prodi));
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $praktikans = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('koordinator.praktikan', compact(
            'praktikums',
            'praktikumId',
            'prodis',
            'praktikans'
        ));
    }

    public function kelompok(Request $request): View
    {
        $this->pastikanKoordinator();

        $praktikums = Praktikum::latest()->get();
        $praktikumId = $request->praktikum_id ?? $this->praktikumAktif()?->id;

        $query = Kelompok::with(['praktikum', 'praktikans.user']);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        if ($request->filled('search')) {
            $query->where('nama_kelompok', 'like', "%{$request->search}%");
        }

        $kelompoks = $query
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('koordinator.kelompok', compact(
            'praktikums',
            'praktikumId',
            'kelompoks'
        ));
    }

    public function storeKelompok(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'nama_kelompok' => ['required', 'string', 'max:255'],
            'sesi' => ['nullable', 'string', 'max:100'],
        ]);

        Kelompok::create($request->only([
            'praktikum_id',
            'nama_kelompok',
            'sesi',
        ]));

        return back()->with('status', 'Kelompok berhasil ditambahkan.');
    }

    public function kelompokDetail(Kelompok $kelompok): View
    {
        $this->pastikanKoordinator();

        $kelompok->load(['praktikum', 'praktikans.user']);

        return view('koordinator.kelompok-detail', compact('kelompok'));
    }

    public function updateKelompok(Request $request, Kelompok $kelompok): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'nama_kelompok' => ['required', 'string', 'max:255'],
            'sesi' => ['nullable', 'string', 'max:100'],
        ]);

        $kelompok->update([
            'nama_kelompok' => $request->nama_kelompok,
            'sesi' => $request->sesi,
        ]);

        return back()->with('status', 'Kelompok berhasil diperbarui.');
    }

    public function penilaian(): View
    {
        $this->pastikanKoordinator();

        return view('koordinator.penilaian');
    }

    public function presensi(Request $request): View
    {
        $this->pastikanKoordinator();

        $praktikums = Praktikum::latest()->get();
        $praktikumId = $request->praktikum_id ?? $this->praktikumAktif()?->id;

        $kelompoks = $praktikumId
            ? Kelompok::where('praktikum_id', $praktikumId)
                ->orderBy('id')
                ->get()
            : collect();

        $sesis = $kelompoks
            ->pluck('sesi')
            ->filter()
            ->unique()
            ->values();

        $query = Presensi::with(['praktikum', 'praktikan.user', 'praktikan.kelompok', 'modul', 'asisten.user']);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kelompok_id')) {
            $kelompok = Kelompok::where('praktikum_id', $praktikumId)
                ->find($request->kelompok_id);

            if ($kelompok) {
                $query->where(function ($q) use ($kelompok) {
                    $q->where('kelompok', $kelompok->nama_kelompok)
                        ->orWhereHas('praktikan', function ($praktikanQuery) use ($kelompok) {
                            $praktikanQuery->where('kelompok_id', $kelompok->id);
                        });
                });
            }
        }

        if ($request->filled('sesi')) {
            $sesi = $request->sesi;

            $query->where(function ($q) use ($sesi) {
                $q->where('sesi', $sesi)
                    ->orWhereHas('praktikan.kelompok', function ($kelompokQuery) use ($sesi) {
                        $kelompokQuery->where('sesi', $sesi);
                    });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('kelompok', 'like', "%{$search}%")
                    ->orWhere('sesi', 'like', "%{$search}%")
                    ->orWhereHas('praktikan.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $presensis = $query
            ->latest('waktu_absen')
            ->paginate(10)
            ->withQueryString();

        return view('koordinator.presensi', compact(
            'praktikums',
            'praktikumId',
            'presensis',
            'kelompoks',
            'sesis'
        ));
    }

    public function nilaiAkhir(Request $request): View
    {
        $this->pastikanKoordinator();

        $praktikums = Praktikum::latest()->get();
        $praktikumId = $request->praktikum_id ?? $this->praktikumAktif()?->id;

        $query = NilaiAkhir::with(['praktikum', 'praktikan.user', 'praktikan.kelompok']);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        if ($request->filled('prodi')) {
            $query->whereHas('praktikan.user', fn ($q) => $q->where('prodi', $request->prodi));
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('praktikan.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $nilaiAkhirs = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $prodis = User::where('role', 'praktikan')
            ->whereNotNull('prodi')
            ->distinct()
            ->orderBy('prodi')
            ->pluck('prodi');

        return view('koordinator.nilai-akhir', compact(
            'praktikums',
            'praktikumId',
            'nilaiAkhirs',
            'prodis'
        ));
    }

    public function exportNilaiAkhir(Request $request)
    {
        $this->pastikanKoordinator();

        $praktikumId = $request->praktikum_id ?? $this->praktikumAktif()?->id;

        $query = NilaiAkhir::with(['praktikan.user', 'praktikan.kelompok']);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        $rows = $query->get();

        $filename = 'nilai-akhir-praktikan.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return Response::stream(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'NIM',
                'Nama',
                'Prodi',
                'Kelompok',
                'Kehadiran',
                'Laporan',
                'Responsi',
                'Asistensi',
                'Kemampuan Praktik',
                'Nilai Angka',
                'Nilai Bobot',
                'Nilai Huruf',
            ]);

            foreach ($rows as $nilai) {
                fputcsv($handle, [
                    $nilai->praktikan->user->nim ?? '-',
                    $nilai->praktikan->user->name ?? '-',
                    $nilai->praktikan->user->prodi ?? '-',
                    $nilai->praktikan->kelompok->nama_kelompok ?? '-',
                    $nilai->kehadiran,
                    $nilai->laporan,
                    $nilai->responsi,
                    $nilai->asistensi,
                    $nilai->kemampuan_praktik,
                    $nilai->nilai_angka,
                    $nilai->nilai_bobot,
                    $nilai->nilai_huruf,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function evaluasiAlgoritma(): View
    {
        $this->pastikanKoordinator();

        $logSearches = LogSearch::latest('waktu_pengujian')
            ->limit(20)
            ->get();

        $logSortings = LogSorting::latest('waktu_pengujian')
            ->limit(20)
            ->get();

        return view('koordinator.evaluasi-algoritma', compact(
            'logSearches',
            'logSortings'
        ));
    }
}