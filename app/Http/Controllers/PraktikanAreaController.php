<?php

namespace App\Http\Controllers;

use App\Models\Asisten;
use App\Models\AsistenPemeriksaLaporan;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\NilaiAkhir;
use App\Models\NilaiHarian;
use App\Models\Praktikan;
use App\Models\Presensi;
use App\Models\JadwalLaporan;
use App\Models\JadwalModulPraktikum;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PraktikanAreaController extends Controller
{
    private function getPraktikan(): Praktikan
    {
        $praktikan = Praktikan::with(['user', 'praktikum', 'kelompok'])
            ->where('user_id', Auth::id())
            ->latest()
            ->firstOrFail();

        if (empty($praktikan->qr_code)) {
            $praktikan->update([
                'qr_code' => $this->buatKodeQrPraktikan($praktikan),
            ]);

            $praktikan->refresh();
            $praktikan->load(['user', 'praktikum', 'kelompok']);
        }

        return $praktikan;
    }

    private function buatKodeQrPraktikan(Praktikan $praktikan): string
    {
        return 'QR-PRAKTIKAN-' .
            $praktikan->praktikum_id . '-' .
            $praktikan->id . '-' .
            strtoupper(Str::random(16));
    }

    public function beranda(): View
    {
        $praktikan = $this->getPraktikan();

        $asisten = Asisten::with('user')
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->where('status', 'aktif')
            ->first();

        $moduls = Modul::where('praktikum_id', $praktikan->praktikum_id)
            ->orderBy('id')
            ->get();

        $nilaiAkhir = NilaiAkhir::where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->first();

        $nilaiHarian = NilaiHarian::where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->first();

        $presensis = Presensi::where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->latest()
            ->get();

        $qrContent = trim((string) $praktikan->qr_code);

        $svg = app('qrcode')
            ->format('svg')
            ->size(360)
            ->margin(5)
            ->errorCorrection('H')
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->generate($qrContent);

        $qrSvg = 'data:image/svg+xml;base64,' . base64_encode($svg);

        return view('praktikan.beranda', compact(
            'praktikan',
            'asisten',
            'moduls',
            'nilaiAkhir',
            'nilaiHarian',
            'presensis',
            'svg',
            'qrSvg',
            'qrContent'
        ));
    }

    public function materi(Request $request): View
    {
        $praktikan = $this->getPraktikan();

        $moduls = Modul::where('praktikum_id', $praktikan->praktikum_id)
            ->orderBy('id')
            ->get();

        $query = Materi::with('modul')
            ->where('praktikum_id', $praktikan->praktikum_id);

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

        return view('praktikan.materi', compact(
            'praktikan',
            'materis',
            'moduls'
        ));
    }

    public function downloadMateri(Materi $materi): RedirectResponse
    {
        $praktikan = $this->getPraktikan();

        abort_if($materi->praktikum_id !== $praktikan->praktikum_id, 403);
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

    public function jadwal(): View
    {
        $praktikan = $this->getPraktikan();

        $asisten = Asisten::with('user')
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->where('status', 'aktif')
            ->first();

        $moduls = Modul::where('praktikum_id', $praktikan->praktikum_id)
            ->orderBy('nomor_modul')
            ->orderBy('id')
            ->get();

        $prodi = $praktikan->user->prodi;
        $kelompokId = $praktikan->kelompok_id;

        $jadwalLaporans = JadwalLaporan::where('praktikum_id', $praktikan->praktikum_id)
            ->where('prodi', $prodi)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get();

        $jadwalModulPraktikums = JadwalModulPraktikum::with(['modul', 'kelompok'])
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->where('prodi', $prodi)
            ->when($kelompokId, function ($query) use ($kelompokId) {
                $query->where('kelompok_id', $kelompokId);
            })
            ->orderBy('minggu_ke')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->orderBy('modul_id')
            ->get()
            ->groupBy('minggu_ke');

        $asistenPemeriksa = AsistenPemeriksaLaporan::with(['asisten.user', 'modul', 'kelompok'])
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->when($kelompokId, function ($query) use ($kelompokId) {
                $query->where('kelompok_id', $kelompokId);
            })
            ->orderBy('modul_id')
            ->get();

        return view('praktikan.jadwal', compact(
            'praktikan',
            'asisten',
            'moduls',
            'jadwalLaporans',
            'jadwalModulPraktikums',
            'asistenPemeriksa'
        ));
    }

    public function biodata(): View
    {
        $praktikan = $this->getPraktikan();

        return view('praktikan.biodata', compact('praktikan'));
    }

    public function updateBiodata(Request $request): RedirectResponse
    {
        $praktikan = $this->getPraktikan();

        $user = User::findOrFail(Auth::id());

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:30', 'unique:users,nim,' . $user->id],
            'prodi' => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'nomor_ponsel' => ['required', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $request->name,
            'nim' => $request->nim,
            'prodi' => $request->prodi,
            'jenis_kelamin' => $request->jenis_kelamin,
            'nomor_ponsel' => $request->nomor_ponsel,
        ]);

        return back()->with('status', 'biodata-updated');
    }

    public function downloadQr()
    {
        $praktikan = $this->getPraktikan();

        $svg = app('qrcode')
            ->format('svg')
            ->size(500)
            ->margin(5)
            ->errorCorrection('H')
            ->color(0, 0, 0)
            ->backgroundColor(255, 255, 255)
            ->generate(trim((string) $praktikan->qr_code));

        $filename = 'qr-code-' . ($praktikan->user->nim ?? $praktikan->id) . '.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}