@extends('layouts.asisten', ['title' => 'Beri Nilai'])

@section('content')
@php
    $formatNilai = function ($value, $decimal = 2) {
        if ($value === null || $value === '') {
            return '-';
        }

        return number_format((float) $value, $decimal);
    };

    $formatSkor = function ($value) {
        if ($value === null || $value === '') {
            return '';
        }

        return $value;
    };

    $nilaiKehadiran = $nilaiAkhir?->kehadiran ?? $nilaiHarian?->rata_rata_kehadiran ?? null;
    $nilaiLaporan = $nilaiAkhir?->laporan ?? $nilaiPenulisanLaporan?->nilai_akhir ?? null;
    $nilaiResponsi = $nilaiAkhir?->responsi ?? $nilaiHarian?->rata_rata_responsi ?? null;
    $nilaiAsistensi = $nilaiAkhir?->asistensi ?? $nilaiHarian?->rata_rata_asistensi ?? null;
    $nilaiKemampuan = $nilaiAkhir?->kemampuan_praktik
        ?? $nilaiHarian?->rata_rata_kemampuan_praktik
        ?? $nilaiKemampuanPraktikum?->rata_rata_akhir
        ?? null;
@endphp

<div class="space-y-8">

    {{-- Header --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">
                    Beri Nilai
                </h1>

                <p class="text-slate-500 mt-2">
                    Kelola nilai praktikan berdasarkan komponen afektif, kognitif, dan psikomotorik.
                </p>
            </div>

            <a href="{{ route('asisten.penilaian') }}"
               class="inline-flex justify-center px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 text-sm">
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                <p class="text-slate-500">Nama</p>
                <p class="font-extrabold text-slate-900 mt-1">{{ $praktikan->user->name ?? '-' }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                <p class="text-slate-500">NIM</p>
                <p class="font-extrabold text-slate-900 mt-1">{{ $praktikan->user->nim ?? '-' }}</p>
            </div>

            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                <p class="text-slate-500">Program Studi</p>
                <p class="font-extrabold text-slate-900 mt-1">{{ $praktikan->user->prodi ?? '-' }}</p>
            </div>

            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-blue-700">Kelompok</p>
                <p class="font-extrabold text-slate-900 mt-1">{{ $praktikan->kelompok->nama_kelompok ?? '-' }}</p>
            </div>

            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-blue-700">Sesi</p>
                <p class="font-extrabold text-slate-900 mt-1">{{ $praktikan->kelompok->sesi ?? '-' }}</p>
            </div>

            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4">
                <p class="text-blue-700">Praktikum</p>
                <p class="font-extrabold text-slate-900 mt-1">{{ $praktikum->nama ?? '-' }}</p>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3">Terjadi kesalahan input:</h2>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Nilai Akhir --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">
                        Nilai Akhir
                    </h2>
                    <p class="text-slate-500 mt-1">
                        Nilai akhir dihitung otomatis dari komponen penilaian dan tidak dapat diubah manual.
                    </p>
                </div>

                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl">
                    📊
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="rounded-3xl bg-blue-50 border border-blue-100 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-blue-700 uppercase">Afektif</span>
                        <span class="px-3 py-1 rounded-full bg-white text-blue-700 text-xs font-extrabold">10%</span>
                    </div>
                    <h3 class="font-extrabold text-slate-900 mt-3">Kehadiran</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">
                        {{ $formatNilai($nilaiKehadiran) }}
                    </p>
                </div>

                <div class="rounded-3xl bg-blue-50 border border-blue-100 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-blue-700 uppercase">Afektif</span>
                        <span class="px-3 py-1 rounded-full bg-white text-blue-700 text-xs font-extrabold">10%</span>
                    </div>
                    <h3 class="font-extrabold text-slate-900 mt-3">Laporan</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">
                        {{ $formatNilai($nilaiLaporan) }}
                    </p>
                </div>

                <div class="rounded-3xl bg-purple-50 border border-purple-100 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-purple-700 uppercase">Kognitif</span>
                        <span class="px-3 py-1 rounded-full bg-white text-purple-700 text-xs font-extrabold">10%</span>
                    </div>
                    <h3 class="font-extrabold text-slate-900 mt-3">Responsi</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">
                        {{ $formatNilai($nilaiResponsi) }}
                    </p>
                </div>

                <div class="rounded-3xl bg-purple-50 border border-purple-100 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-purple-700 uppercase">Kognitif</span>
                        <span class="px-3 py-1 rounded-full bg-white text-purple-700 text-xs font-extrabold">20%</span>
                    </div>
                    <h3 class="font-extrabold text-slate-900 mt-3">Asistensi</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">
                        {{ $formatNilai($nilaiAsistensi) }}
                    </p>
                </div>

                <div class="rounded-3xl bg-cyan-50 border border-cyan-100 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-cyan-700 uppercase">Psikomotorik</span>
                        <span class="px-3 py-1 rounded-full bg-white text-cyan-700 text-xs font-extrabold">50%</span>
                    </div>
                    <h3 class="font-extrabold text-slate-900 mt-3">Kemampuan Praktik</h3>
                    <p class="text-3xl font-extrabold text-slate-900 mt-4">
                        {{ $formatNilai($nilaiKemampuan) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-3xl border border-blue-100 bg-blue-100 p-6">
                    <p class="text-blue-700 text-sm font-semibold">Nilai Angka</p>
                    <p class="text-4xl font-extrabold text-slate-900 mt-2">
                        {{ $formatNilai($nilaiAkhir?->nilai_angka ?? null) }}
                    </p>
                </div>

                <div class="rounded-3xl border bg-purple-100 border-purple-100 p-6">
                    <p class="text-purple-700 text-sm font-semibold">Nilai Bobot</p>
                    <p class="text-4xl font-extrabold text-slate-900 mt-2">
                        {{ $formatNilai($nilaiAkhir?->nilai_bobot ?? null, 1) }}
                    </p>
                </div>

                <div class="rounded-3xl border bg-cyan-100 border-cyan-100 p-6">
                    <p class="text-teal-600 text-sm font-semibold">Nilai Huruf</p>
                    <p class="text-4xl font-extrabold text-slate-900 mt-2">
                        {{ $nilaiAkhir?->nilai_huruf ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Nilai Harian --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Nilai Harian
            </h2>
            <p class="text-slate-500 mt-1">
                Input nilai harian berdasarkan kelompok komponen penilaian.
            </p>
        </div>

        <form id="form-nilai-harian" method="POST" action="{{ route('asisten.penilaian.store', $praktikan) }}">
            @csrf
            <input type="hidden" name="kategori" value="nilai_harian">

            <div class="p-6 space-y-6">

                {{-- Afektif --}}
                <div class="rounded-3xl border border-blue-100 bg-blue-50/40 p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                        <div>
                            <p class="text-xs font-extrabold text-blue-700 uppercase">Afektif</p>
                            <h3 class="text-xl font-extrabold text-slate-900 mt-1">
                                Kehadiran dan Laporan
                            </h3>
                        </div>

                        <div class="px-4 py-2 rounded-2xl bg-white border border-blue-100 text-blue-700 font-extrabold text-sm">
                            Kehadiran 10% · Laporan 10%
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                        <div class="rounded-3xl bg-white border border-blue-100 p-5 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                <h4 class="font-extrabold text-slate-900">Kehadiran</h4>
                                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-bold">
                                    Rata-rata: {{ $formatNilai($nilaiHarian?->rata_rata_kehadiran) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach ($moduls as $modul)
                                    @php
                                        $statusKehadiran = data_get($nilaiHarian?->kehadiran, (string) $modul->id);
                                    @endphp

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">
                                            Pertemuan {{ $loop->iteration }}
                                        </label>

                                        <select name="kehadiran[{{ $modul->id }}]"
                                                class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">-</option>
                                            <option value="hadir" @selected($statusKehadiran === 'hadir')>Hadir</option>
                                            <option value="sakit" @selected($statusKehadiran === 'sakit')>Sakit</option>
                                            <option value="izin" @selected($statusKehadiran === 'izin')>Izin</option>
                                            <option value="alpa" @selected($statusKehadiran === 'alpa')>Alpa</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-3xl bg-white border border-blue-100 p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-extrabold text-slate-900">Laporan Harian</h4>
                                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-bold">
                                    Bobot 10%
                                </span>
                            </div>

                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Nilai Rata-rata Laporan
                            </label>

                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   name="laporan"
                                   value="{{ $nilaiHarian?->laporan ?? '' }}"
                                   class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Kognitif --}}
                <div class="rounded-3xl border border-purple-100 bg-purple-50/40 p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                        <div>
                            <p class="text-xs font-extrabold text-purple-700 uppercase">Kognitif</p>
                            <h3 class="text-xl font-extrabold text-slate-900 mt-1">
                                Responsi dan Proses Asistensi
                            </h3>
                        </div>

                        <div class="px-4 py-2 rounded-2xl bg-white border border-purple-100 text-purple-700 font-extrabold text-sm">
                            Responsi 10% · Asistensi 20%
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                        <div class="rounded-3xl bg-white border border-purple-100 p-5 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                <h4 class="font-extrabold text-slate-900">Responsi</h4>
                                <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-sm font-bold">
                                    Rata-rata: {{ $formatNilai($nilaiHarian?->rata_rata_responsi) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach ($moduls as $modul)
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">
                                            Modul {{ $loop->iteration }}
                                        </label>

                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               name="responsi[{{ $modul->id }}]"
                                               value="{{ data_get($nilaiHarian?->responsi, (string) $modul->id) }}"
                                               class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-3xl bg-white border border-purple-100 p-5 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                <h4 class="font-extrabold text-slate-900">Proses Asistensi</h4>
                                <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-sm font-bold">
                                    Rata-rata: {{ $formatNilai($nilaiHarian?->rata_rata_asistensi) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach ($moduls as $modul)
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">
                                            Modul {{ $loop->iteration }}
                                        </label>

                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               name="asistensi[{{ $modul->id }}]"
                                               value="{{ data_get($nilaiHarian?->asistensi, (string) $modul->id) }}"
                                               class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Psikomotorik --}}
                <div class="rounded-3xl border border-cyan-100 bg-cyan-50/40 p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                        <div>
                            <p class="text-xs font-extrabold text-cyan-700 uppercase">Psikomotorik</p>
                            <h3 class="text-xl font-extrabold text-slate-900 mt-1">
                                Kemampuan Praktik
                            </h3>
                        </div>

                        <div class="px-4 py-2 rounded-2xl bg-white border border-cyan-100 text-cyan-700 font-extrabold text-sm">
                            Bobot 50%
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white border border-cyan-100 p-5 shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <h4 class="font-extrabold text-slate-900">Input Kemampuan Praktik Harian</h4>

                            <span class="px-3 py-1 rounded-full bg-cyan-100 text-cyan-700 text-sm font-bold">
                                Rata-rata: {{ $formatNilai($nilaiHarian?->rata_rata_kemampuan_praktik ?? $nilaiKemampuanPraktikum?->rata_rata_akhir) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach ($moduls as $modul)
                                @php
                                    $nilaiKemampuanModul = data_get($nilaiHarian?->kemampuan_praktik, (string) $modul->id);

                                    if ($nilaiKemampuanModul === null || $nilaiKemampuanModul === '') {
                                        $nilaiKemampuanModul = data_get($nilaiKemampuanPraktikum?->nilai_modul, $modul->id . '.rerata');
                                    }
                                @endphp

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">
                                        Modul {{ $loop->iteration }}
                                    </label>

                                    <input type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        name="kemampuan_praktik[{{ $modul->id }}]"
                                        value="{{ $nilaiKemampuanModul }}"
                                        class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                        Simpan Nilai Harian
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- Nilai Kemampuan Praktikum --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Nilai Kemampuan Praktikum
            </h2>

            <p class="text-slate-500 mt-1">
                Isi skor K1 sampai K4 berdasarkan kemampuan praktikan saat mengikuti praktikum. Nilai rerata akan dihitung otomatis.
            </p>

            <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                <span class="text-slate-500 font-semibold">
                    Keterangan skor:
                </span>

                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold">
                    3 = Baik
                </span>

                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold">
                    2 = Cukup
                </span>

                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold">
                    1 = Kurang
                </span>
            </div>
        </div>

        <form id="form-kemampuan-praktik" method="POST" action="{{ route('asisten.penilaian.store', $praktikan) }}">
            @csrf
            <input type="hidden" name="kategori" value="kemampuan_praktik">

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
                    @foreach ($moduls as $modul)
                        <div class="rounded-3xl border border-cyan-100 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p class="text-xs font-extrabold text-cyan-700 uppercase">
                                        Psikomotorik
                                    </p>
                                    <h3 class="font-extrabold text-slate-900 mt-1">
                                        Modul {{ $loop->iteration }}
                                    </h3>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs text-slate-500">Rerata</p>
                                    <p class="text-2xl font-extrabold text-slate-900">
                                        {{ $formatNilai(data_get($nilaiKemampuanPraktikum?->nilai_modul, $modul->id . '.rerata')) }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">K1 · 10%</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="3"
                                           name="nilai_modul[{{ $modul->id }}][k1]"
                                           value="{{ $formatSkor(data_get($nilaiKemampuanPraktikum?->nilai_modul, $modul->id . '.k1')) }}"
                                           class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">K2 · 20%</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="3"
                                           name="nilai_modul[{{ $modul->id }}][k2]"
                                           value="{{ $formatSkor(data_get($nilaiKemampuanPraktikum?->nilai_modul, $modul->id . '.k2')) }}"
                                           class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">K3 · 20%</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="3"
                                           name="nilai_modul[{{ $modul->id }}][k3]"
                                           value="{{ $formatSkor(data_get($nilaiKemampuanPraktikum?->nilai_modul, $modul->id . '.k3')) }}"
                                           class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-1">K4 · 50%</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="3"
                                           name="nilai_modul[{{ $modul->id }}][k4]"
                                           value="{{ $formatSkor(data_get($nilaiKemampuanPraktikum?->nilai_modul, $modul->id . '.k4')) }}"
                                           class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 rounded-3xl bg-cyan-50 border border-cyan-100 p-5">
                    <div>
                        <p class="text-sm text-cyan-700 font-bold">Rata-rata Akhir Kemampuan Praktikum</p>
                        <p class="text-3xl font-extrabold text-slate-900 mt-1">
                            {{ $formatNilai($nilaiKemampuanPraktikum?->rata_rata_akhir) }}
                        </p>
                    </div>

                    <button type="submit"
                            class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                        Simpan Kemampuan Praktikum
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- Nilai Laporan Resmi --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Nilai Laporan Resmi
            </h2>
            <p class="text-slate-500 mt-1">
                Input nilai laporan resmi untuk setiap modul.
            </p>
        </div>

        <form id="form-laporan-resmi" method="POST" action="{{ route('asisten.penilaian.store', $praktikan) }}">
            @csrf
            <input type="hidden" name="kategori" value="laporan_resmi">

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($moduls as $modul)
                        <div class="rounded-3xl bg-white border border-blue-100 p-5 shadow-sm">
                            <label class="block text-sm font-extrabold text-slate-900 mb-2">
                                Modul {{ $loop->iteration }}
                            </label>

                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   name="nilai_modul[{{ $modul->id }}]"
                                   value="{{ data_get($nilaiLaporanResmi?->nilai_modul, (string) $modul->id) }}"
                                   class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 rounded-3xl bg-blue-50 border border-blue-100 p-5">
                    <div>
                        <p class="text-sm text-blue-700 font-bold">Rata-rata Laporan Resmi</p>
                        <p class="text-3xl font-extrabold text-slate-900 mt-1">
                            {{ $formatNilai($nilaiLaporanResmi?->rata_rata) }}
                        </p>
                    </div>

                    <button type="submit"
                            class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                        Simpan Laporan Resmi
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- Nilai Penulisan Laporan --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Nilai Penulisan Laporan
            </h2>
            <p class="text-slate-500 mt-1">
                Nilai akhir laporan dihitung dari rata-rata mingguan dan laporan resmi.
            </p>
        </div>

        <form id="form-penulisan-laporan" method="POST" action="{{ route('asisten.penilaian.store', $praktikan) }}">
            @csrf
            <input type="hidden" name="kategori" value="penulisan_laporan">

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($moduls as $modul)
                        <div class="rounded-3xl bg-white border border-blue-100 p-5 shadow-sm">
                            <label class="block text-sm font-extrabold text-slate-900 mb-2">
                                Modul {{ $loop->iteration }}
                            </label>

                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   name="laporan_mingguan[{{ $modul->id }}]"
                                   value="{{ data_get($nilaiPenulisanLaporan?->laporan_mingguan, (string) $modul->id) }}"
                                   class="w-full rounded-2xl border-slate-200 text-center focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-3xl bg-slate-50 border border-slate-100 p-5">
                        <p class="text-sm text-slate-500">Rata-rata Mingguan</p>
                        <p class="text-3xl font-extrabold text-slate-900 mt-1">
                            {{ $formatNilai($nilaiPenulisanLaporan?->rata_rata_mingguan) }}
                        </p>
                        <p class="text-xs text-slate-500 mt-2">Bobot 50%</p>
                    </div>

                    <div class="rounded-3xl bg-slate-50 border border-slate-100 p-5">
                        <p class="text-sm text-slate-500">Laporan Resmi</p>
                        <p class="text-3xl font-extrabold text-slate-900 mt-1">
                            {{ $formatNilai($nilaiPenulisanLaporan?->rata_rata_resmi ?? $nilaiLaporanResmi?->rata_rata) }}
                        </p>
                        <p class="text-xs text-slate-500 mt-2">Bobot 50%</p>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-amber-50 p-5">
                        <p class="text-sm text-amber-700 font-semibold">Nilai Akhir Laporan</p>
                        <p class="text-3xl font-extrabold text-slate-900 mt-1">
                            {{ $formatNilai($nilaiPenulisanLaporan?->nilai_akhir) }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                        Simpan Penulisan Laporan
                    </button>
                </div>
            </div>
        </form>
    </section>

</div>
@endsection