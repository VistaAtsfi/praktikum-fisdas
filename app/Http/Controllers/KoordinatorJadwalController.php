<?php

namespace App\Http\Controllers;

use App\Models\JadwalLaporan;
use App\Models\JadwalModulPraktikum;
use App\Models\Kelompok;
use App\Models\Modul;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\PraktikumProdi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KoordinatorJadwalController extends Controller
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

    private function daftarHari(): array
    {
        return [
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu',
            'Minggu',
        ];
    }

    public function index(Request $request): View
    {
        $this->pastikanKoordinator();

        $praktikums = Praktikum::latest()->get();
        $praktikumId = $request->praktikum_id ?? $this->praktikumAktif()?->id;
        $selectedProdi = $request->prodi;

        $prodisDariPeriode = $praktikumId
            ? PraktikumProdi::where('praktikum_id', $praktikumId)->pluck('nama_prodi')
            : collect();

        $prodisDariPraktikan = $praktikumId
            ? Praktikan::with('user')
                ->where('praktikum_id', $praktikumId)
                ->get()
                ->pluck('user.prodi')
            : collect();

        $prodisDariKelompok = $praktikumId
            ? Kelompok::where('praktikum_id', $praktikumId)->pluck('prodi')
            : collect();

        $prodis = $prodisDariPeriode
            ->merge($prodisDariPraktikan)
            ->merge($prodisDariKelompok)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $moduls = $praktikumId
            ? Modul::where('praktikum_id', $praktikumId)
                ->orderBy('nomor_modul')
                ->orderBy('id')
                ->get()
            : collect();

        $kelompoks = $praktikumId
            ? Kelompok::where('praktikum_id', $praktikumId)
                ->when($selectedProdi, function ($query) use ($selectedProdi) {
                    $query->where('prodi', $selectedProdi);
                })
                ->orderBy('prodi')
                ->orderByRaw('CAST(REPLACE(nama_kelompok, "Kelompok ", "") AS UNSIGNED)')
                ->orderBy('nama_kelompok')
                ->get()
            : collect();

        $jadwalLaporans = JadwalLaporan::query()
            ->when($praktikumId, function ($query) use ($praktikumId) {
                $query->where('praktikum_id', $praktikumId);
            })
            ->when($selectedProdi, function ($query) use ($selectedProdi) {
                $query->where('prodi', $selectedProdi);
            })
            ->orderBy('prodi')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->orderBy('jam_selesai')
            ->get();

        $jadwalModulPraktikums = JadwalModulPraktikum::with(['modul', 'kelompok'])
            ->when($praktikumId, function ($query) use ($praktikumId) {
                $query->where('praktikum_id', $praktikumId);
            })
            ->when($selectedProdi, function ($query) use ($selectedProdi) {
                $query->where('prodi', $selectedProdi);
            })
            ->orderBy('prodi')
            ->orderBy('minggu_ke')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->orderBy('jam_selesai')
            ->orderBy('modul_id')
            ->get();

        $jadwalModulByProdi = $jadwalModulPraktikums
            ->groupBy('prodi')
            ->map(function ($items) {
                return $items->groupBy('minggu_ke');
            });

        $hariOptions = $this->daftarHari();

        return view('koordinator.jadwal', compact(
            'praktikums',
            'praktikumId',
            'selectedProdi',
            'prodis',
            'moduls',
            'kelompoks',
            'jadwalLaporans',
            'jadwalModulPraktikums',
            'jadwalModulByProdi',
            'hariOptions'
        ));
    }

    public function storeLaporan(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'prodi' => ['required', 'string', 'max:100'],
            'hari' => ['required', Rule::in($this->daftarHari())],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        JadwalLaporan::updateOrCreate(
            [
                'praktikum_id' => $request->praktikum_id,
                'prodi' => $request->prodi,
                'hari' => $request->hari,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ],
            [
                'keterangan' => $request->keterangan,
            ]
        );

        return back()->with('status', 'Jadwal laporan berhasil disimpan.');
    }

    public function updateLaporan(Request $request, JadwalLaporan $jadwalLaporan): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'prodi' => ['required', 'string', 'max:100'],
            'hari' => ['required', Rule::in($this->daftarHari())],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $jadwalLaporan->update([
            'praktikum_id' => $request->praktikum_id,
            'prodi' => $request->prodi,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('status', 'Jadwal laporan berhasil diperbarui.');
    }

    public function deleteLaporan(JadwalLaporan $jadwalLaporan): RedirectResponse
    {
        $this->pastikanKoordinator();

        $jadwalLaporan->delete();

        return back()->with('status', 'Jadwal laporan berhasil dihapus.');
    }

    public function storeModul(Request $request): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'prodi' => ['required', 'string', 'max:100'],
            'minggu_ke' => ['required', 'integer', 'min:1', 'max:20'],
            'hari' => ['required', Rule::in($this->daftarHari())],
            'modul_id' => [
                'required',
                Rule::exists('moduls', 'id')
                    ->where(fn ($query) => $query->where('praktikum_id', $request->praktikum_id)),
            ],
            'kelompok_id' => [
                'required',
                Rule::exists('kelompoks', 'id')
                    ->where(fn ($query) => $query->where('praktikum_id', $request->praktikum_id)),
            ],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $kelompok = Kelompok::where('praktikum_id', $request->praktikum_id)
            ->where('prodi', $request->prodi)
            ->findOrFail($request->kelompok_id);

        $modul = Modul::where('praktikum_id', $request->praktikum_id)
            ->findOrFail($request->modul_id);

        JadwalModulPraktikum::updateOrCreate(
            [
                'praktikum_id' => $request->praktikum_id,
                'prodi' => $request->prodi,
                'minggu_ke' => $request->minggu_ke,
                'hari' => $request->hari,
                'modul_id' => $modul->id,
                'kelompok_id' => $kelompok->id,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ],
            [
                'kelompok_label' => $kelompok->nama_kelompok,
                'keterangan' => $request->keterangan,
            ]
        );

        return back()->with('status', 'Jadwal modul dan sesi berhasil disimpan.');
    }

    public function updateModul(Request $request, JadwalModulPraktikum $jadwalModulPraktikum): RedirectResponse
    {
        $this->pastikanKoordinator();

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'prodi' => ['required', 'string', 'max:100'],
            'minggu_ke' => ['required', 'integer', 'min:1', 'max:20'],
            'hari' => ['required', Rule::in($this->daftarHari())],
            'modul_id' => [
                'required',
                Rule::exists('moduls', 'id')
                    ->where(fn ($query) => $query->where('praktikum_id', $request->praktikum_id)),
            ],
            'kelompok_id' => [
                'required',
                Rule::exists('kelompoks', 'id')
                    ->where(fn ($query) => $query->where('praktikum_id', $request->praktikum_id)),
            ],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $kelompok = Kelompok::where('praktikum_id', $request->praktikum_id)
            ->where('prodi', $request->prodi)
            ->findOrFail($request->kelompok_id);

        $modul = Modul::where('praktikum_id', $request->praktikum_id)
            ->findOrFail($request->modul_id);

        $jadwalModulPraktikum->update([
            'praktikum_id' => $request->praktikum_id,
            'prodi' => $request->prodi,
            'minggu_ke' => $request->minggu_ke,
            'hari' => $request->hari,
            'modul_id' => $modul->id,
            'kelompok_id' => $kelompok->id,
            'kelompok_label' => $kelompok->nama_kelompok,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('status', 'Jadwal modul dan sesi berhasil diperbarui.');
    }

    public function deleteModul(JadwalModulPraktikum $jadwalModulPraktikum): RedirectResponse
    {
        $this->pastikanKoordinator();

        $jadwalModulPraktikum->delete();

        return back()->with('status', 'Jadwal modul dan sesi berhasil dihapus.');
    }
}