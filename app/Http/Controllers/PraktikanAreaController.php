<?php

namespace App\Http\Controllers;

use App\Models\Asisten;
use App\Models\Materi;
use App\Models\Modul;
use App\Models\NilaiAkhir;
use App\Models\NilaiHarian;
use App\Models\Praktikan;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\LogSearch;
use App\Models\LogSorting;

class PraktikanAreaController extends Controller
{
    private function getPraktikan(): Praktikan
    {
        return Praktikan::with(['user', 'praktikum', 'kelompok'])
            ->where('user_id', Auth::id())
            ->latest()
            ->firstOrFail();
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

        $svg = app('qrcode')
            ->format('svg')
            ->size(220)
            ->margin(2)
            ->generate($praktikan->qr_code);

        return view('praktikan.beranda', compact(
            'praktikan',
            'asisten',
            'moduls',
            'nilaiAkhir',
            'nilaiHarian',
            'presensis',
            'svg'
        ));
    }

    public function materi(Request $request): View
    {
        $praktikan = $this->getPraktikan();

        /*
        |--------------------------------------------------------------------------
        | Ambil data awal dari database
        |--------------------------------------------------------------------------
        | Sorting dan searching tidak dilakukan di query database.
        | Data diubah menjadi array, lalu diproses di lapisan aplikasi.
        */
        $materisQuery = Materi::with('modul')
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->get();

        $materisArray = $materisQuery->map(function ($materi) {
            return [
                'id' => $materi->id,
                'judul' => $materi->judul,
                'deskripsi' => $materi->deskripsi,
                'link' => $materi->link,
                'created_at' => $materi->created_at,
                'modul_nama' => $materi->modul->nama_modul ?? 'Materi Umum',
                'modul_id' => $materi->modul->id ?? 999,
                'model' => $materi,
            ];
        })->toArray();

        $keyword = $request->query('q');
        $sortBy = $request->query('sort_by', 'modul_id');
        $sortDirection = $request->query('sort_direction', 'asc');

        /*
        |--------------------------------------------------------------------------
        | Sequential Search
        |--------------------------------------------------------------------------
        */
        $searchStart = microtime(true);
        $searchMemoryStart = memory_get_usage();

        $searchResult = $this->sequentialSearchMateri($materisArray, $keyword);

        $materisArray = $searchResult['data'];

        $searchEnd = microtime(true);
        $searchMemoryEnd = memory_get_usage();

        if ($keyword) {
            LogSearch::create([
                'jumlah_data' => count($materisQuery),
                'field_pencarian' => 'judul, deskripsi, modul',
                'kata_kunci_pencarian' => $keyword,
                'waktu_pemrosesan_server' => round(($searchEnd - $searchStart) * 1000, 4),
                'jumlah_iterasi' => $searchResult['iterations'],
                'jumlah_perbandingan' => $searchResult['comparisons'],
                'penggunaan_memori' => abs($searchMemoryEnd - $searchMemoryStart),
                'waktu_pengujian' => now(),
                'keterangan_pengujian' => 'Sequential Search materi praktikum pada lapisan aplikasi.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Merge Sort
        |--------------------------------------------------------------------------
        */
        $sortingStart = microtime(true);
        $sortingMemoryStart = memory_get_usage();

        $sortResult = $this->mergeSortMateri($materisArray, $sortBy, $sortDirection);

        $materisArray = $sortResult['data'];

        $sortingEnd = microtime(true);
        $sortingMemoryEnd = memory_get_usage();

        LogSorting::create([
            'jumlah_data' => count($materisArray),
            'field_pengurutan' => $sortBy,
            'arah_pengurutan' => $sortDirection,
            'waktu_pemrosesan_server' => round(($sortingEnd - $sortingStart) * 1000, 4),
            'jumlah_perbandingan' => $sortResult['comparisons'],
            'penggunaan_memori' => abs($sortingMemoryEnd - $sortingMemoryStart),
            'waktu_pengujian' => now(),
            'keterangan_pengujian' => 'Merge Sort materi praktikum pada lapisan aplikasi.',
        ]);

        $materis = collect($materisArray)->pluck('model');

        $moduls = Modul::where('praktikum_id', $praktikan->praktikum_id)
            ->orderBy('id')
            ->get();

        return view('praktikan.materi', compact(
            'praktikan',
            'materis',
            'moduls',
            'keyword',
            'sortBy',
            'sortDirection',
            'searchResult',
            'sortResult'
        ));
    }

    public function jadwal(): View
    {
        $praktikan = $this->getPraktikan();

        $asisten = Asisten::with('user')
            ->where('praktikum_id', $praktikan->praktikum_id)
            ->where('status', 'aktif')
            ->first();

        $moduls = Modul::where('praktikum_id', $praktikan->praktikum_id)
            ->orderBy('id')
            ->get();

        return view('praktikan.jadwal', compact('praktikan', 'asisten', 'moduls'));
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
            ->size(350)
            ->margin(2)
            ->generate($praktikan->qr_code);

        $filename = 'qr-code-' . ($praktikan->user->nim ?? $praktikan->id) . '.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function sequentialSearchMateri(array $data, ?string $keyword): array
    {
        $iterations = 0;
        $comparisons = 0;

        if (!$keyword) {
            return [
                'data' => $data,
                'iterations' => 0,
                'comparisons' => 0,
            ];
        }

        $keyword = strtolower(trim($keyword));
        $result = [];

        foreach ($data as $item) {
            $iterations++;

            $judul = strtolower($item['judul'] ?? '');
            $deskripsi = strtolower($item['deskripsi'] ?? '');
            $modul = strtolower($item['modul_nama'] ?? '');

            $comparisons++;
            $matchJudul = str_contains($judul, $keyword);

            $comparisons++;
            $matchDeskripsi = str_contains($deskripsi, $keyword);

            $comparisons++;
            $matchModul = str_contains($modul, $keyword);

            if ($matchJudul || $matchDeskripsi || $matchModul) {
                $result[] = $item;
            }
        }

        return [
            'data' => $result,
            'iterations' => $iterations,
            'comparisons' => $comparisons,
        ];
    }

    private function mergeSortMateri(array $data, string $sortBy, string $direction = 'asc'): array
    {
        $comparisons = 0;

        $allowedFields = [
            'judul',
            'modul_nama',
            'modul_id',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedFields)) {
            $sortBy = 'modul_urutan';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $sorted = $this->mergeSortRecursive($data, $sortBy, $direction, $comparisons);

        return [
            'data' => $sorted,
            'comparisons' => $comparisons,
        ];
    }

    private function mergeSortRecursive(array $data, string $sortBy, string $direction, int &$comparisons): array
    {
        if (count($data) <= 1) {
            return $data;
        }

        $middle = intdiv(count($data), 2);

        $left = array_slice($data, 0, $middle);
        $right = array_slice($data, $middle);

        $left = $this->mergeSortRecursive($left, $sortBy, $direction, $comparisons);
        $right = $this->mergeSortRecursive($right, $sortBy, $direction, $comparisons);

        return $this->mergeMateri($left, $right, $sortBy, $direction, $comparisons);
    }

    private function mergeMateri(array $left, array $right, string $sortBy, string $direction, int &$comparisons): array
    {
        $result = [];

        while (count($left) > 0 && count($right) > 0) {
            $comparisons++;

            $leftValue = $left[0][$sortBy] ?? '';
            $rightValue = $right[0][$sortBy] ?? '';

            if ($sortBy === 'created_at') {
                $leftValue = strtotime((string) $leftValue);
                $rightValue = strtotime((string) $rightValue);
            }

            if (is_string($leftValue)) {
                $leftValue = strtolower($leftValue);
            }

            if (is_string($rightValue)) {
                $rightValue = strtolower($rightValue);
            }

            $condition = $direction === 'asc'
                ? $leftValue <= $rightValue
                : $leftValue >= $rightValue;

            if ($condition) {
                $result[] = array_shift($left);
            } else {
                $result[] = array_shift($right);
            }
        }

        return array_merge($result, $left, $right);
    }
}