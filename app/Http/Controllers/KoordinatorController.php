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
use App\Models\AsistenPemeriksaLaporan;
use App\Models\KategoriPenilaian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
                    ->orWhere('prodi', 'like', "%{$search}%")
                    ->orWhere('nomor_ponsel', 'like', "%{$search}%")
                    ->orWhere('jenis_kelamin', 'like', "%{$search}%");
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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

        $sortDirection = $request->sort_direction === 'desc' ? 'desc' : 'asc';

        $kelompoks = $praktikumId
            ? Kelompok::where('praktikum_id', $praktikumId)
                ->orderBy('nama_kelompok')
                ->get()
            : collect();

        $sesis = $kelompoks
            ->pluck('sesi')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $prodis = $praktikumId
            ? Praktikan::with('user')
                ->where('praktikum_id', $praktikumId)
                ->get()
                ->pluck('user.prodi')
                ->filter()
                ->unique()
                ->sort()
                ->values()
            : collect();

        $query = Praktikan::with(['user', 'praktikum', 'kelompok']);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        if ($request->filled('prodi')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('prodi', $request->prodi);
            });
        }

        if ($request->filled('kelompok_id')) {
            $query->where('kelompok_id', $request->kelompok_id);
        }

        if ($request->filled('sesi')) {
            $query->whereHas('kelompok', function ($q) use ($request) {
                $q->where('sesi', $request->sesi);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Sequential Search + Merge Sort
        |--------------------------------------------------------------------------
        | Search hanya berdasarkan NIM dan nama.
        | Filter kelompok dan sesi memakai dropdown.
        | Sorting memakai Merge Sort berdasarkan prodi dan 3 angka terakhir NIM.
        */
        $semuaPraktikans = $query->get();

        $hasilSearch = $this->sequentialSearchPraktikanKoordinator(
            $semuaPraktikans,
            $request->search
        );

        $hasilSort = $this->mergeSortPraktikanByNim(
            $hasilSearch,
            $sortDirection
        );

        $praktikans = $this->paginateCollectionKoordinator(
            $hasilSort,
            $request,
            200
        );

        return view('koordinator.praktikan', compact(
            'praktikums',
            'praktikumId',
            'prodis',
            'kelompoks',
            'sesis',
            'praktikans',
            'sortDirection'
        ));
    }

    public function kelompok(Request $request): View
    {
        $this->pastikanKoordinator();

        $praktikums = Praktikum::latest()->get();
        $praktikumId = $request->praktikum_id ?? $this->praktikumAktif()?->id;
        $selectedProdi = $request->prodi;
        $selectedSesi = $request->sesi;

        $moduls = $praktikumId
            ? Modul::where('praktikum_id', $praktikumId)->orderBy('id')->get()
            : collect();

        $prodisDariKelompok = $praktikumId
            ? Kelompok::where('praktikum_id', $praktikumId)
                ->whereNotNull('prodi')
                ->pluck('prodi')
            : collect();

        $prodisDariPraktikan = $praktikumId
            ? Praktikan::with('user')
                ->where('praktikum_id', $praktikumId)
                ->get()
                ->pluck('user.prodi')
            : collect();

        $prodis = $prodisDariKelompok
            ->merge($prodisDariPraktikan)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $sesis = $praktikumId
            ? Kelompok::where('praktikum_id', $praktikumId)
                ->whereNotNull('sesi')
                ->pluck('sesi')
                ->filter()
                ->unique()
                ->sort()
                ->values()
            : collect();

        $kelompokQuery = Kelompok::with(['praktikans.user'])
            ->when($praktikumId, fn ($query) => $query->where('praktikum_id', $praktikumId))
            ->when($selectedProdi, fn ($query) => $query->where('prodi', $selectedProdi))
            ->when($selectedSesi, fn ($query) => $query->where('sesi', $selectedSesi));

        if ($request->filled('search')) {
            $search = $request->search;

            $kelompokQuery->where(function ($query) use ($search) {
                $query->where('nama_kelompok', 'like', "%{$search}%")
                    ->orWhere('prodi', 'like', "%{$search}%")
                    ->orWhere('sesi', 'like', "%{$search}%");
            });
        }

        $kelompokRows = $kelompokQuery
            ->orderBy('id')
            ->get();

        $praktikanQuery = Praktikan::with(['user', 'kelompok'])
            ->when($praktikumId, fn ($query) => $query->where('praktikum_id', $praktikumId))
            ->whereNotNull('kelompok_id')
            ->when($selectedProdi, function ($query) use ($selectedProdi) {
                $query->whereHas('user', fn ($userQuery) => $userQuery->where('prodi', $selectedProdi));
            })
            ->when($selectedSesi, function ($query) use ($selectedSesi) {
                $query->whereHas('kelompok', fn ($kelompokQuery) => $kelompokQuery->where('sesi', $selectedSesi));
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $praktikanQuery->where(function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('nim', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('prodi', 'like', "%{$search}%");
                })->orWhereHas('kelompok', function ($kelompokQuery) use ($search) {
                    $kelompokQuery->where('nama_kelompok', 'like', "%{$search}%")
                        ->orWhere('sesi', 'like', "%{$search}%");
                });
            });
        }

        $praktikanRows = $praktikanQuery
            ->get()
            ->sort(function ($a, $b) {
                $kelompokA = $a->kelompok->nama_kelompok ?? '';
                $kelompokB = $b->kelompok->nama_kelompok ?? '';

                $compareKelompok = strnatcasecmp($kelompokA, $kelompokB);

                if ($compareKelompok !== 0) {
                    return $compareKelompok;
                }

                return strcmp($a->user->nim ?? '', $b->user->nim ?? '');
            })
            ->values();

        $asistensAktif = $praktikumId
            ? Asisten::with('user')
                ->where('praktikum_id', $praktikumId)
                ->where('status', 'aktif')
                ->orderBy('id')
                ->get()
            : collect();

        $asistenPemeriksaMap = collect();

        if ($praktikumId && $kelompokRows->isNotEmpty()) {
            $asistenPemeriksaMap = AsistenPemeriksaLaporan::where('praktikum_id', $praktikumId)
                ->whereIn('kelompok_id', $kelompokRows->pluck('id'))
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->kelompok_id . '-' . $item->modul_id => $item->asisten_id,
                    ];
                });
        }

        return view('koordinator.kelompok', compact(
            'praktikums',
            'praktikumId',
            'selectedProdi',
            'selectedSesi',
            'prodis',
            'sesis',
            'moduls',
            'kelompokRows',
            'praktikanRows',
            'asistensAktif',
            'asistenPemeriksaMap'
        ));
    }

    public function storeKelompok(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'prodi' => ['required', 'string', 'max:100'],
            'nama_kelompok' => ['required', 'string', 'max:255'],
            'sesi' => ['nullable', 'string', 'max:100'],
            'nim_awal' => ['nullable', 'string', 'max:30', 'required_with:nim_akhir'],
            'nim_akhir' => ['nullable', 'string', 'max:30', 'required_with:nim_awal'],
        ]);

        $kelompok = Kelompok::create([
            'praktikum_id' => $request->praktikum_id,
            'prodi' => $request->prodi,
            'nama_kelompok' => $request->nama_kelompok,
            'sesi' => $request->sesi,
        ]);

        $jumlahDipindahkan = $this->assignPraktikanByNimRange(
            $kelompok,
            $request->nim_awal,
            $request->nim_akhir
        );

        $pesan = 'Kelompok berhasil ditambahkan.';

        if ($jumlahDipindahkan > 0) {
            $pesan .= " {$jumlahDipindahkan} praktikan berhasil dimasukkan ke kelompok ini.";
        }

        return back()->with('status', $pesan);
    }

    public function kelompokDetail(Kelompok $kelompok): View
    {
        $this->pastikanKoordinator();

        $kelompok->load([
            'praktikum',
            'praktikans.user',
        ]);

        $moduls = Modul::where('praktikum_id', $kelompok->praktikum_id)
            ->orderBy('id')
            ->get();

        $asistensAktif = Asisten::with('user')
            ->where('praktikum_id', $kelompok->praktikum_id)
            ->where('status', 'aktif')
            ->orderBy('id')
            ->get();

        $asistenPemeriksaMap = AsistenPemeriksaLaporan::where('praktikum_id', $kelompok->praktikum_id)
            ->where('kelompok_id', $kelompok->id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->modul_id => $item->asisten_id,
                ];
            });

        return view('koordinator.kelompok-detail', compact(
            'kelompok',
            'moduls',
            'asistensAktif',
            'asistenPemeriksaMap'
        ));
    }

    public function updateKelompok(Request $request, Kelompok $kelompok): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'prodi' => ['required', 'string', 'max:100'],
            'nama_kelompok' => ['required', 'string', 'max:255'],
            'sesi' => ['nullable', 'string', 'max:100'],
            'nim_awal' => ['nullable', 'string', 'max:30', 'required_with:nim_akhir'],
            'nim_akhir' => ['nullable', 'string', 'max:30', 'required_with:nim_awal'],
        ]);

        $kelompok->update([
            'prodi' => $request->prodi,
            'nama_kelompok' => $request->nama_kelompok,
            'sesi' => $request->sesi,
        ]);

        AsistenPemeriksaLaporan::where('kelompok_id', $kelompok->id)
            ->update([
                'prodi' => $request->prodi,
            ]);

        $jumlahDipindahkan = $this->assignPraktikanByNimRange(
            $kelompok,
            $request->nim_awal,
            $request->nim_akhir
        );

        $pesan = 'Kelompok berhasil diperbarui.';

        if ($jumlahDipindahkan > 0) {
            $pesan .= " {$jumlahDipindahkan} praktikan berhasil dimasukkan atau dipindahkan ke kelompok ini.";
        }

        return back()->with('status', $pesan);
    }

    public function storeAsistenPemeriksa(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'kelompok_id' => ['required', 'exists:kelompoks,id'],
        ]);

        $kelompok = Kelompok::where('praktikum_id', $request->praktikum_id)
            ->findOrFail($request->kelompok_id);

        /*
        |--------------------------------------------------------------------------
        | Simpan banyak modul sekaligus
        |--------------------------------------------------------------------------
        | Dipakai untuk halaman detail kelompok dan tabel kelompok.
        */
        if ($request->has('asisten_pemeriksa')) {
            $request->validate([
                'asisten_pemeriksa' => ['nullable', 'array'],
                'asisten_pemeriksa.*' => ['nullable', 'exists:asistens,id'],
            ]);

            foreach ($request->input('asisten_pemeriksa', []) as $modulId => $asistenId) {
                $modul = Modul::where('praktikum_id', $request->praktikum_id)
                    ->findOrFail($modulId);

                if (!empty($asistenId)) {
                    Asisten::where('praktikum_id', $request->praktikum_id)
                        ->where('status', 'aktif')
                        ->findOrFail($asistenId);

                    AsistenPemeriksaLaporan::updateOrCreate(
                        [
                            'praktikum_id' => $request->praktikum_id,
                            'kelompok_id' => $kelompok->id,
                            'modul_id' => $modul->id,
                        ],
                        [
                            'asisten_id' => $asistenId,
                            'prodi' => $kelompok->prodi,
                        ]
                    );
                } else {
                    AsistenPemeriksaLaporan::where('praktikum_id', $request->praktikum_id)
                        ->where('kelompok_id', $kelompok->id)
                        ->where('modul_id', $modul->id)
                        ->delete();
                }
            }

            return back()->with('status', 'Asisten pemeriksa berhasil disimpan.');
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan satu modul
        |--------------------------------------------------------------------------
        | Cadangan jika masih ada form lama yang mengirim modul_id dan asisten_id.
        */
        $request->validate([
            'modul_id' => ['required', 'exists:moduls,id'],
            'asisten_id' => ['nullable', 'exists:asistens,id'],
        ]);

        $modul = Modul::where('praktikum_id', $request->praktikum_id)
            ->findOrFail($request->modul_id);

        if ($request->filled('asisten_id')) {
            Asisten::where('praktikum_id', $request->praktikum_id)
                ->where('status', 'aktif')
                ->findOrFail($request->asisten_id);

            AsistenPemeriksaLaporan::updateOrCreate(
                [
                    'praktikum_id' => $request->praktikum_id,
                    'kelompok_id' => $kelompok->id,
                    'modul_id' => $modul->id,
                ],
                [
                    'asisten_id' => $request->asisten_id,
                    'prodi' => $kelompok->prodi,
                ]
            );
        } else {
            AsistenPemeriksaLaporan::where('praktikum_id', $request->praktikum_id)
                ->where('kelompok_id', $kelompok->id)
                ->where('modul_id', $modul->id)
                ->delete();
        }

        return back()->with('status', 'Asisten pemeriksa berhasil disimpan.');
    }

    public function penilaian(): View
    {
        $this->pastikanKoordinator();

        $this->pastikanKategoriPenilaianDefault();

        $kategoriPenilaians = KategoriPenilaian::query()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        $kategoriAktif = $kategoriPenilaians
            ->where('is_aktif', true)
            ->values();

        $totalPersentase = $kategoriAktif
            ->sum(fn ($kategori) => (float) $kategori->persentase);

        return view('koordinator.penilaian', compact(
            'kategoriPenilaians',
            'kategoriAktif',
            'totalPersentase'
        ));
    }

    public function storeKategoriPenilaian(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'tipe_penilaian' => ['required', 'in:afektif,kognitif,psikomotorik'],
            'nama_subjek' => ['required', 'string', 'max:255'],
            'persentase' => ['required', 'numeric', 'min:0', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        KategoriPenilaian::create([
            'tipe_penilaian' => $request->tipe_penilaian,
            'nama_subjek' => $request->nama_subjek,
            'persentase' => $request->persentase,
            'keterangan' => $request->keterangan,
            'urutan' => $request->urutan,
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return back()->with('status', 'Subjek penilaian berhasil ditambahkan.');
    }

    public function updateKategoriPenilaian(Request $request, KategoriPenilaian $kategoriPenilaian): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'tipe_penilaian' => ['required', 'in:afektif,kognitif,psikomotorik'],
            'nama_subjek' => ['required', 'string', 'max:255'],
            'persentase' => ['required', 'numeric', 'min:0', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'urutan' => ['required', 'integer', 'min:1'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $kategoriPenilaian->update([
            'tipe_penilaian' => $request->tipe_penilaian,
            'nama_subjek' => $request->nama_subjek,
            'persentase' => $request->persentase,
            'keterangan' => $request->keterangan,
            'urutan' => $request->urutan,
            'is_aktif' => $request->boolean('is_aktif'),
        ]);

        return back()->with('status', 'Subjek penilaian berhasil diperbarui.');
    }

    public function deleteKategoriPenilaian(KategoriPenilaian $kategoriPenilaian): RedirectResponse
    {
        $this->pastikanKoordinator();

        $kategoriPenilaian->delete();

        return back()->with('status', 'Subjek penilaian berhasil dihapus.');
    }

    private function pastikanKategoriPenilaianDefault(): void
    {
        if (KategoriPenilaian::count() > 0) {
            return;
        }

        $defaults = [
            [
                'tipe_penilaian' => 'afektif',
                'nama_subjek' => 'Kehadiran',
                'persentase' => 10,
                'keterangan' => 'Nilai kehadiran praktikan pada setiap modul praktikum.',
                'urutan' => 1,
                'is_aktif' => true,
            ],
            [
                'tipe_penilaian' => 'afektif',
                'nama_subjek' => 'Laporan',
                'persentase' => 10,
                'keterangan' => 'Nilai laporan praktikum yang berasal dari laporan mingguan dan laporan resmi.',
                'urutan' => 2,
                'is_aktif' => true,
            ],
            [
                'tipe_penilaian' => 'kognitif',
                'nama_subjek' => 'Responsi',
                'persentase' => 10,
                'keterangan' => 'Nilai responsi praktikan pada setiap modul.',
                'urutan' => 3,
                'is_aktif' => true,
            ],
            [
                'tipe_penilaian' => 'kognitif',
                'nama_subjek' => 'Asistensi',
                'persentase' => 20,
                'keterangan' => 'Nilai proses asistensi atau bimbingan laporan praktikum.',
                'urutan' => 4,
                'is_aktif' => true,
            ],
            [
                'tipe_penilaian' => 'psikomotorik',
                'nama_subjek' => 'Kemampuan Praktik',
                'persentase' => 50,
                'keterangan' => 'Nilai kemampuan praktik berdasarkan penilaian K1 sampai K4.',
                'urutan' => 5,
                'is_aktif' => true,
            ],
        ];

        foreach ($defaults as $default) {
            KategoriPenilaian::create($default);
        }
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

        $moduls = $praktikumId
            ? Modul::where('praktikum_id', $praktikumId)
                ->orderBy('id')
                ->get()
            : collect();

        $prodis = $praktikumId
            ? Praktikan::with('user')
                ->where('praktikum_id', $praktikumId)
                ->get()
                ->pluck('user.prodi')
                ->filter()
                ->unique()
                ->sort()
                ->values()
            : collect();

        $query = Presensi::with([
            'praktikum',
            'praktikan.user',
            'praktikan.kelompok',
            'modul',
            'asisten.user',
        ]);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        if ($request->filled('modul_id')) {
            $query->where('modul_id', $request->modul_id);
        }

        if ($request->filled('prodi')) {
            $query->whereHas('praktikan.user', function ($userQuery) use ($request) {
                $userQuery->where('prodi', $request->prodi);
            });
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
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('prodi', 'like', "%{$search}%");
                    })
                    ->orWhereHas('modul', function ($modulQuery) use ($search) {
                        $modulQuery->where('nama_modul', 'like', "%{$search}%");
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
            'sesis',
            'moduls',
            'prodis'
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

    private function assignPraktikanByNimRange(Kelompok $kelompok, ?string $nimAwal, ?string $nimAkhir): int
    {
        if (trim((string) $nimAwal) === '' || trim((string) $nimAkhir) === '') {
            return 0;
        }

        $nimAwal = preg_replace('/\D/', '', (string) $nimAwal);
        $nimAkhir = preg_replace('/\D/', '', (string) $nimAkhir);

        if ($nimAwal === '' || $nimAkhir === '') {
            throw ValidationException::withMessages([
                'nim_awal' => 'NIM awal dan NIM akhir harus diisi dengan NIM lengkap.',
            ]);
        }

        if (strlen($nimAwal) !== strlen($nimAkhir)) {
            throw ValidationException::withMessages([
                'nim_awal' => 'Panjang NIM awal dan NIM akhir harus sama.',
            ]);
        }

        if (strlen($nimAwal) < 4 || strlen($nimAkhir) < 4) {
            throw ValidationException::withMessages([
                'nim_awal' => 'Masukkan NIM lengkap, contoh: 2209106001.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Format NIM
        |--------------------------------------------------------------------------
        | Contoh:
        | 2209106001 sampai 2209106005
        |
        | Awalan  : 2209106
        | Urutan  : 001 sampai 005
        */
        $prefixAwal = substr($nimAwal, 0, -3);
        $prefixAkhir = substr($nimAkhir, 0, -3);

        if ($prefixAwal !== $prefixAkhir) {
            throw ValidationException::withMessages([
                'nim_awal' => 'Awalan NIM harus sama. Contoh yang benar: 2209106001 sampai 2209106005.',
            ]);
        }

        $urutAwal = (int) substr($nimAwal, -3);
        $urutAkhir = (int) substr($nimAkhir, -3);

        if ($urutAwal > $urutAkhir) {
            throw ValidationException::withMessages([
                'nim_awal' => 'Nomor urut NIM awal tidak boleh lebih besar dari NIM akhir.',
            ]);
        }

        $query = Praktikan::with('user')
            ->where('praktikum_id', $kelompok->praktikum_id)
            ->whereHas('user', function ($query) use ($prefixAwal, $kelompok) {
                $query->whereNotNull('nim')
                    ->where('nim', 'like', $prefixAwal . '%');

                if (!empty($kelompok->prodi)) {
                    $query->where('prodi', $kelompok->prodi);
                }
            });

        $praktikanIds = $query->get()
            ->filter(function ($praktikan) use ($prefixAwal, $urutAwal, $urutAkhir) {
                $nim = preg_replace('/\D/', '', (string) ($praktikan->user->nim ?? ''));

                if ($nim === '') {
                    return false;
                }

                $prefix = substr($nim, 0, -3);
                $urut = (int) substr($nim, -3);

                return $prefix === $prefixAwal
                    && $urut >= $urutAwal
                    && $urut <= $urutAkhir;
            })
            ->pluck('id')
            ->values();

        if ($praktikanIds->isEmpty()) {
            return 0;
        }

        return Praktikan::whereIn('id', $praktikanIds)
            ->update([
                'kelompok_id' => $kelompok->id,
            ]);
    }

    private function sequentialSearchPraktikanKoordinator($praktikans, ?string $keyword)
    {
        $praktikans = collect($praktikans)->values();

        if (!$keyword) {
            return $praktikans;
        }

        $keyword = strtolower(trim($keyword));
        $hasil = collect();

        foreach ($praktikans as $praktikan) {
            $nim = strtolower((string) ($praktikan->user->nim ?? ''));
            $nama = strtolower((string) ($praktikan->user->name ?? ''));

            if (
                str_contains($nim, $keyword) ||
                str_contains($nama, $keyword)
            ) {
                $hasil->push($praktikan);
            }
        }

        return $hasil->values();
    }

    private function mergeSortPraktikanByNim($praktikans, string $direction = 'asc')
    {
        $praktikans = collect($praktikans)->values();

        if ($praktikans->count() <= 1) {
            return $praktikans;
        }

        $mid = intdiv($praktikans->count(), 2);

        $left = $this->mergeSortPraktikanByNim(
            $praktikans->slice(0, $mid)->values(),
            $direction
        );

        $right = $this->mergeSortPraktikanByNim(
            $praktikans->slice($mid)->values(),
            $direction
        );

        return $this->mergePraktikanByNim($left, $right, $direction);
    }

    private function mergePraktikanByNim($left, $right, string $direction = 'asc')
    {
        $result = collect();

        $i = 0;
        $j = 0;

        while ($i < $left->count() && $j < $right->count()) {
            if ($this->ambilKiriUntukSortingPraktikan($left[$i], $right[$j], $direction)) {
                $result->push($left[$i]);
                $i++;
            } else {
                $result->push($right[$j]);
                $j++;
            }
        }

        while ($i < $left->count()) {
            $result->push($left[$i]);
            $i++;
        }

        while ($j < $right->count()) {
            $result->push($right[$j]);
            $j++;
        }

        return $result->values();
    }

    private function ambilKiriUntukSortingPraktikan($left, $right, string $direction = 'asc'): bool
    {
        $prodiLeft = strtolower((string) ($left->user->prodi ?? ''));
        $prodiRight = strtolower((string) ($right->user->prodi ?? ''));

        /*
        |--------------------------------------------------------------------------
        | Urutan prodi tetap A-Z
        |--------------------------------------------------------------------------
        | Jadi kalau belum filter prodi, tampilannya tetap:
        | Prodi A: 001, 002, 003, ...
        | Prodi B: 001, 002, 003, ...
        */
        if ($prodiLeft !== $prodiRight) {
            return $prodiLeft <= $prodiRight;
        }

        $nomorLeft = $this->ambilTigaAngkaTerakhirNim($left->user->nim ?? null);
        $nomorRight = $this->ambilTigaAngkaTerakhirNim($right->user->nim ?? null);

        if ($nomorLeft === $nomorRight) {
            $nimLeft = preg_replace('/\D/', '', (string) ($left->user->nim ?? ''));
            $nimRight = preg_replace('/\D/', '', (string) ($right->user->nim ?? ''));

            return $direction === 'desc'
                ? $nimLeft >= $nimRight
                : $nimLeft <= $nimRight;
        }

        return $direction === 'desc'
            ? $nomorLeft >= $nomorRight
            : $nomorLeft <= $nomorRight;
    }

    private function ambilTigaAngkaTerakhirNim(?string $nim): int
    {
        $nim = preg_replace('/\D/', '', (string) $nim);

        if ($nim === '') {
            return PHP_INT_MAX;
        }

        return (int) substr($nim, -3);
    }

    private function paginateCollectionKoordinator($items, Request $request, int $perPage = 10): LengthAwarePaginator
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
}