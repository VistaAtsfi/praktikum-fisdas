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

        $totalJadwalModul = $jadwalModulPraktikums->count();
        $totalJadwalLaporan = $jadwalLaporans->count();
        $totalKelompokTerjadwal = $jadwalModulPraktikums->pluck('kelompok_id')->unique()->count();

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
            'hariOptions',
            'totalJadwalModul',
            'totalJadwalLaporan',
            'totalKelompokTerjadwal'
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
            'jam_selesai' => ['required', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->jam_selesai <= $request->jam_mulai) {
            return back()
                ->withInput()
                ->withErrors([
                    'jam_selesai' => 'Jam selesai harus lebih akhir dari jam mulai.',
                ]);
        }

        // Cek tabrakan jam pengumpulan laporan pada prodi dan hari yang sama
        $konflik = JadwalLaporan::where('praktikum_id', $request->praktikum_id)
            ->where('prodi', $request->prodi)
            ->where('hari', $request->hari)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->first();

        if ($konflik) {
            return back()
                ->withInput()
                ->withErrors([
                    'bentrok' => "Tabrakan Jadwal Laporan: Prodi {$request->prodi} sudah memiliki jadwal laporan pada hari {$request->hari} pukul " . substr($konflik->jam_mulai, 0, 5) . " - " . substr($konflik->jam_selesai, 0, 5) . "!",
                ]);
        }

        JadwalLaporan::create([
            'praktikum_id' => $request->praktikum_id,
            'prodi' => $request->prodi,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('status', 'Jadwal laporan berhasil disimpan.');
    }

    public function updateLaporan(Request $request, JadwalLaporan $jadwalLaporan): RedirectResponse
    {
        $this->pastikanKoordinator();

        $praktikumId = $request->praktikum_id ?? $jadwalLaporan->praktikum_id;
        $request->merge(['praktikum_id' => $praktikumId]);

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'prodi' => ['required', 'string', 'max:100'],
            'hari' => ['required', Rule::in($this->daftarHari())],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->jam_selesai <= $request->jam_mulai) {
            return back()
                ->withInput()
                ->withErrors([
                    'jam_selesai' => 'Jam selesai harus lebih akhir dari jam mulai.',
                ]);
        }

        // Cek tabrakan jam pengumpulan laporan (abaikan ID yang sedang diupdate)
        $konflik = JadwalLaporan::where('praktikum_id', $praktikumId)
            ->where('prodi', $request->prodi)
            ->where('hari', $request->hari)
            ->where('id', '!=', $jadwalLaporan->id)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->first();

        if ($konflik) {
            return back()
                ->withInput()
                ->withErrors([
                    'bentrok' => "Tabrakan Jadwal Laporan: Prodi {$request->prodi} sudah memiliki jadwal laporan pada hari {$request->hari} pukul " . substr($konflik->jam_mulai, 0, 5) . " - " . substr($konflik->jam_selesai, 0, 5) . "!",
                ]);
        }

        $jadwalLaporan->update([
            'praktikum_id' => $praktikumId,
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
            'minggu_ke' => ['required', 'integer', 'min:1', 'max:4'],
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
            'jam_selesai' => ['required', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->jam_selesai <= $request->jam_mulai) {
            return back()
                ->withInput()
                ->withErrors([
                    'jam_selesai' => 'Jam selesai harus lebih akhir dari jam mulai.',
                ]);
        }

        $kelompok = Kelompok::where('praktikum_id', $request->praktikum_id)
            ->where('prodi', $request->prodi)
            ->findOrFail($request->kelompok_id);

        $modul = Modul::where('praktikum_id', $request->praktikum_id)
            ->findOrFail($request->modul_id);

        // 1. Cek tabrakan jam pada KELOMPOK yang sama
        $konflikKelompok = JadwalModulPraktikum::where('praktikum_id', $request->praktikum_id)
            ->where('hari', $request->hari)
            ->where('minggu_ke', $request->minggu_ke)
            ->where('kelompok_id', $kelompok->id)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->with('modul')
            ->first();

        if ($konflikKelompok) {
            return back()
                ->withInput()
                ->withErrors([
                    'bentrok' => "Tabrakan Jadwal Kelompok: {$kelompok->nama_kelompok} ({$kelompok->prodi}) sudah memiliki jadwal Modul {$konflikKelompok->modul?->nomor_modul} pada hari {$request->hari}, Minggu ke-{$request->minggu_ke} pukul " . substr($konflikKelompok->jam_mulai, 0, 5) . " - " . substr($konflikKelompok->jam_selesai, 0, 5) . ". Kelompok tidak boleh memiliki jadwal ganda pada jam yang sama!",
                ]);
        }

        // 2. Cek tabrakan MODUL pada prodi yang sama di jam yang sama
        $konflikModul = JadwalModulPraktikum::where('praktikum_id', $request->praktikum_id)
            ->where('prodi', $request->prodi)
            ->where('hari', $request->hari)
            ->where('minggu_ke', $request->minggu_ke)
            ->where('modul_id', $modul->id)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->with('kelompok')
            ->first();

        if ($konflikModul) {
            return back()
                ->withInput()
                ->withErrors([
                    'bentrok' => "Tabrakan Penggunaan Modul: Modul {$modul->nomor_modul} ({$modul->nama_modul}) pada Prodi {$request->prodi} sudah digunakan oleh {$konflikModul->kelompok?->nama_kelompok} pada hari {$request->hari}, Minggu ke-{$request->minggu_ke} pukul " . substr($konflikModul->jam_mulai, 0, 5) . " - " . substr($konflikModul->jam_selesai, 0, 5) . ". Setiap modul hanya dapat digunakan oleh 1 kelompok pada jam yang sama!",
                ]);
        }

        // 3. Cek tabrakan SESI berbeda pada prodi yang sama di jam yang sama
        if (!empty($kelompok->sesi)) {
            $konflikSesi = JadwalModulPraktikum::where('praktikum_id', $request->praktikum_id)
                ->where('prodi', $request->prodi)
                ->where('hari', $request->hari)
                ->where('minggu_ke', $request->minggu_ke)
                ->whereHas('kelompok', function ($q) use ($kelompok) {
                    $q->whereNotNull('sesi')
                      ->where('sesi', '!=', '')
                      ->where('sesi', '!=', $kelompok->sesi);
                })
                ->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                      ->where('jam_selesai', '>', $request->jam_mulai);
                })
                ->with('kelompok')
                ->first();

            if ($konflikSesi) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'bentrok' => "Tabrakan Sesi Praktikum: {$kelompok->nama_kelompok} ({$kelompok->sesi}) bertabrakan jam dengan {$konflikSesi->kelompok?->nama_kelompok} ({$konflikSesi->kelompok?->sesi}) pada hari {$request->hari}, Minggu ke-{$request->minggu_ke} pukul " . substr($konflikSesi->jam_mulai, 0, 5) . " - " . substr($konflikSesi->jam_selesai, 0, 5) . ". Kelompok dari sesi berbeda tidak boleh berada di jam yang sama!",
                    ]);
            }
        }

        JadwalModulPraktikum::create([
            'praktikum_id' => $request->praktikum_id,
            'prodi' => $request->prodi,
            'minggu_ke' => $request->minggu_ke,
            'hari' => $request->hari,
            'modul_id' => $modul->id,
            'kelompok_id' => $kelompok->id,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kelompok_label' => $kelompok->nama_kelompok,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('status', "Jadwal praktikum {$kelompok->nama_kelompok} (Modul {$modul->nomor_modul}) berhasil disimpan.");
    }

    public function updateModul(Request $request, JadwalModulPraktikum $jadwalModulPraktikum): RedirectResponse
    {
        $this->pastikanKoordinator();

        $praktikumId = $request->praktikum_id ?? $jadwalModulPraktikum->praktikum_id;
        $hari = $request->hari ?? $jadwalModulPraktikum->hari ?? 'Senin';

        $request->merge([
            'praktikum_id' => $praktikumId,
            'hari' => $hari,
        ]);

        $request->validate([
            'praktikum_id' => ['required', 'exists:praktikums,id'],
            'prodi' => ['required', 'string', 'max:100'],
            'minggu_ke' => ['required', 'integer', 'min:1', 'max:4'],
            'hari' => ['required', Rule::in($this->daftarHari())],
            'modul_id' => [
                'required',
                Rule::exists('moduls', 'id')
                    ->where(fn ($query) => $query->where('praktikum_id', $praktikumId)),
            ],
            'kelompok_id' => [
                'required',
                Rule::exists('kelompoks', 'id')
                    ->where(fn ($query) => $query->where('praktikum_id', $praktikumId)),
            ],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->jam_selesai <= $request->jam_mulai) {
            return back()
                ->withInput()
                ->withErrors([
                    'jam_selesai' => 'Jam selesai harus lebih akhir dari jam mulai.',
                ]);
        }

        $kelompok = Kelompok::where('praktikum_id', $praktikumId)
            ->where('prodi', $request->prodi)
            ->findOrFail($request->kelompok_id);

        $modul = Modul::where('praktikum_id', $praktikumId)
            ->findOrFail($request->modul_id);

        // 1. Cek tabrakan jam pada KELOMPOK yang sama (abaikan ID yang sedang diupdate)
        $konflikKelompok = JadwalModulPraktikum::where('praktikum_id', $praktikumId)
            ->where('hari', $hari)
            ->where('minggu_ke', $request->minggu_ke)
            ->where('kelompok_id', $kelompok->id)
            ->where('id', '!=', $jadwalModulPraktikum->id)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->with('modul')
            ->first();

        if ($konflikKelompok) {
            return back()
                ->withInput()
                ->withErrors([
                    'bentrok' => "Tabrakan Jadwal Kelompok: {$kelompok->nama_kelompok} ({$kelompok->prodi}) sudah memiliki jadwal Modul {$konflikKelompok->modul?->nomor_modul} pada hari {$hari}, Minggu ke-{$request->minggu_ke} pukul " . substr($konflikKelompok->jam_mulai, 0, 5) . " - " . substr($konflikKelompok->jam_selesai, 0, 5) . ". Kelompok tidak boleh memiliki jadwal ganda pada jam yang sama!",
                ]);
        }

        // 2. Cek tabrakan MODUL pada prodi yang sama (abaikan ID yang sedang diupdate)
        $konflikModul = JadwalModulPraktikum::where('praktikum_id', $praktikumId)
            ->where('prodi', $request->prodi)
            ->where('hari', $hari)
            ->where('minggu_ke', $request->minggu_ke)
            ->where('modul_id', $modul->id)
            ->where('id', '!=', $jadwalModulPraktikum->id)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->with('kelompok')
            ->first();

        if ($konflikModul) {
            return back()
                ->withInput()
                ->withErrors([
                    'bentrok' => "Tabrakan Penggunaan Modul: Modul {$modul->nomor_modul} ({$modul->nama_modul}) pada Prodi {$request->prodi} sudah digunakan oleh {$konflikModul->kelompok?->nama_kelompok} pada hari {$hari}, Minggu ke-{$request->minggu_ke} pukul " . substr($konflikModul->jam_mulai, 0, 5) . " - " . substr($konflikModul->jam_selesai, 0, 5) . ". Setiap modul hanya dapat digunakan oleh 1 kelompok pada jam yang sama!",
                ]);
        }

        // 3. Cek tabrakan SESI berbeda pada prodi yang sama (abaikan ID yang sedang diupdate)
        if (!empty($kelompok->sesi)) {
            $konflikSesi = JadwalModulPraktikum::where('praktikum_id', $praktikumId)
                ->where('prodi', $request->prodi)
                ->where('hari', $hari)
                ->where('minggu_ke', $request->minggu_ke)
                ->where('id', '!=', $jadwalModulPraktikum->id)
                ->whereHas('kelompok', function ($q) use ($kelompok) {
                    $q->whereNotNull('sesi')
                      ->where('sesi', '!=', '')
                      ->where('sesi', '!=', $kelompok->sesi);
                })
                ->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                      ->where('jam_selesai', '>', $request->jam_mulai);
                })
                ->with('kelompok')
                ->first();

            if ($konflikSesi) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'bentrok' => "Tabrakan Sesi Praktikum: {$kelompok->nama_kelompok} ({$kelompok->sesi}) bertabrakan jam dengan {$konflikSesi->kelompok?->nama_kelompok} ({$konflikSesi->kelompok?->sesi}) pada hari {$hari}, Minggu ke-{$request->minggu_ke} pukul " . substr($konflikSesi->jam_mulai, 0, 5) . " - " . substr($konflikSesi->jam_selesai, 0, 5) . ". Kelompok dari sesi berbeda tidak boleh berada di jam yang sama!",
                    ]);
            }
        }

        $jadwalModulPraktikum->update([
            'praktikum_id' => $praktikumId,
            'prodi' => $request->prodi,
            'minggu_ke' => $request->minggu_ke,
            'hari' => $hari,
            'modul_id' => $modul->id,
            'kelompok_id' => $kelompok->id,
            'kelompok_label' => $kelompok->nama_kelompok,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('status', "Jadwal praktikum {$kelompok->nama_kelompok} (Modul {$modul->nomor_modul}) berhasil diperbarui.");
    }

    public function deleteModul(JadwalModulPraktikum $jadwalModulPraktikum): RedirectResponse
    {
        $this->pastikanKoordinator();

        $jadwalModulPraktikum->delete();

        return back()->with('status', 'Jadwal modul dan sesi berhasil dihapus.');
    }
}