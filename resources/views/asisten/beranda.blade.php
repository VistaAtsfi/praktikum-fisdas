@extends('layouts.asisten', ['title' => 'Beranda Asisten Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-gradient-to-br from-blue-900 via-blue-800 to-cyan-700 rounded-3xl p-8 text-white shadow-xl overflow-hidden relative">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>

        <div class="relative">
            <p class="text-blue-100 font-semibold mb-2">
                Selamat datang,
            </p>

            <h1 class="text-3xl md:text-4xl font-extrabold mb-4">
                {{ auth()->user()->name }}
            </h1>

            <p class="text-blue-100 leading-relaxed max-w-3xl">
                Di halaman beranda Asisten Praktikum Fisika Dasar ini, Anda dapat memantau data praktikan, presensi, materi, dan penilaian praktikum.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                    <p class="text-blue-100 text-sm">Praktikum</p>
                    <p class="font-extrabold">{{ $praktikum->nama ?? '-' }}</p>
                </div>

                <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                    <p class="text-blue-100 text-sm">Kelompok</p>
                    <p class="font-extrabold">{{ $kelompoks->count() }} Kelompok</p>
                </div>

                <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                    <p class="text-blue-100 text-sm">Praktikan</p>
                    <p class="font-extrabold">{{ $praktikans->count() }} Praktikan</p>
                </div>

                <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                    <p class="text-blue-100 text-sm">Status</p>
                    <p class="font-extrabold">{{ ucfirst($asisten->status ?? '-') }}</p>
                </div>
            </div>
        </div>
    </section>

    @if (!$asisten)
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="text-xl font-extrabold text-red-700">
                Data asisten belum tersedia
            </h2>
            <p class="text-red-600 mt-2">
                Akun ini memiliki role asisten, tetapi belum terhubung ke data asisten dan praktikum.
            </p>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 text-center mb-6">
            Profil
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <p><span class="font-bold text-slate-700">Email:</span> {{ auth()->user()->email }}</p>
                <p><span class="font-bold text-slate-700">Nama Lengkap:</span> {{ auth()->user()->name }}</p>
                <p><span class="font-bold text-slate-700">NIM:</span> {{ auth()->user()->nim ?? '-' }}</p>
                <p><span class="font-bold text-slate-700">Program Studi:</span> {{ auth()->user()->prodi ?? '-' }}</p>
            </div>

            <div class="space-y-3">
                <p class="text-slate-800">
                    <span class="font-bold text-slate-900">Jenis Kelamin:</span>
                    <span class="font-normal text-slate-800">
                        {{ $asisten->user->jenis_kelamin === 'L' ? 'Laki-laki' : ($asisten->user->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                    </span>
                </p>
                <p><span class="font-bold text-slate-700">Nomor Ponsel:</span> {{ auth()->user()->nomor_ponsel ?? '-' }}</p>
                <p><span class="font-bold text-slate-700">Praktikum:</span> {{ $praktikum->nama ?? '-' }}</p>
                <p><span class="font-bold text-slate-700">Status:</span> {{ ucfirst($asisten->status ?? '-') }}</p>
            </div>
        </div>
        <div class="mt-6 rounded-2xl bg-blue-50 border border-blue-100 px-5 py-4">
            <p class="text-sm text-blue-800 leading-relaxed">
                <span class="font-extrabold">Catatan:</span>
                Data profil asisten dikelola oleh koordinator. Jika terdapat kesalahan data atau ingin melakukan perubahan, silakan hubungi koordinator.
            </p>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden"
             x-data="{
                activeProdiTab: '{{ $praktikanBelumAsistensiByProdi->keys()->first() ?? '' }}'
             }">
        
        {{-- Section Header & KPI Stats --}}
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900 text-center mb-6">
                Data Aktivitas Praktikum
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-center">
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Jumlah Kelompok</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-2">{{ $kelompoks->count() }}</h3>
                </div>

                <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-center">
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Jumlah Praktikan</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-2">{{ $praktikans->count() }}</h3>
                </div>

                <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-center">
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Jumlah Materi</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-2">{{ $jumlahMateri }}</h3>
                </div>

                <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-center">
                    <p class="text-xs font-bold text-blue-700 uppercase tracking-wider">Total Presensi</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-2">{{ $jumlahPresensi }}</h3>
                </div>
            </div>
        </div>

        {{-- Sub-header Monitoring Belum Asistensi --}}
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <span>⚠️</span>
                    <span>Praktikan yang Belum Melakukan Asistensi</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Daftar praktikan yang belum memiliki nilai asistensi pada satu atau lebih modul praktikum.
                </p>
            </div>

            <span class="text-xs font-black px-3.5 py-1.5 rounded-full {{ $praktikanBelumAsistensi->count() > 0 ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }} self-start sm:self-auto">
                Total: {{ $praktikanBelumAsistensi->count() }} Praktikan
            </span>
        </div>

        @if ($praktikanBelumAsistensiByProdi->isNotEmpty())
            {{-- Tab Navigasi Per Prodi --}}
            <div class="bg-slate-50/70 border-b border-slate-200 px-6 py-3.5">
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider shrink-0 mr-2 flex items-center gap-1.5">
                        <span>🏛️</span>
                        <span>Program Studi:</span>
                    </span>

                    @foreach ($praktikanBelumAsistensiByProdi as $prodiName => $listPraktikan)
                        <button type="button"
                                @click="activeProdiTab = '{{ $prodiName }}'"
                                :class="activeProdiTab === '{{ $prodiName }}'
                                    ? 'bg-blue-700 text-white shadow-md'
                                    : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200 hover:border-blue-200'"
                                class="px-4 py-2 rounded-2xl text-xs font-extrabold transition-all duration-150 shrink-0 flex items-center gap-2 cursor-pointer">
                            <span>{{ $prodiName }}</span>
                            <span :class="activeProdiTab === '{{ $prodiName }}' ? 'bg-blue-800/80 text-blue-100' : 'bg-amber-100 text-amber-800'"
                                  class="px-2 py-0.5 rounded-lg text-[10px] font-black">
                                {{ $listPraktikan->count() }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Isi Tabel per Prodi --}}
            <div class="p-6">
                @foreach ($praktikanBelumAsistensiByProdi as $prodiName => $listPraktikan)
                    <div x-cloak
                         x-show="activeProdiTab === '{{ $prodiName }}'"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-4">

                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-black text-slate-800 flex items-center gap-2">
                                <span>🏛️</span>
                                <span>{{ $prodiName }}</span>
                                <span class="text-xs font-bold text-slate-500">({{ $listPraktikan->count() }} praktikan belum tuntas asistensi)</span>
                            </h4>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-xs">
                            <table class="w-full min-w-[850px] text-sm">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-700 font-extrabold text-xs uppercase tracking-wider">
                                        <th class="p-3.5 text-center">NIM</th>
                                        <th class="p-3.5 text-center">Nama</th>
                                        <th class="p-3.5 text-center">Kelompok</th>
                                        <th class="p-3.5 text-center">No. Ponsel</th>
                                        <th class="p-3.5 text-center">Email</th>
                                        <th class="p-3.5 text-center">Belum Asistensi</th>
                                        <th class="p-3.5 text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @forelse ($listPraktikan as $praktikan)
                                        <tr class="hover:bg-blue-50/40 transition">
                                            <td class="p-3.5 text-center font-bold text-slate-800">{{ $praktikan->user->nim ?? '-' }}</td>
                                            <td class="p-3.5 text-center font-semibold text-slate-900">{{ $praktikan->user->name ?? '-' }}</td>
                                            <td class="p-3.5 text-center">
                                                <span class="inline-block px-2.5 py-1 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 text-xs font-bold">
                                                    {{ $praktikan->kelompok?->nama_kelompok ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="p-3.5 text-center text-slate-600">{{ $praktikan->user->nomor_ponsel ?? '-' }}</td>
                                            <td class="p-3.5 text-center text-slate-600">{{ $praktikan->user->email ?? '-' }}</td>
                                            <td class="p-3.5">
                                                <div class="flex flex-wrap gap-1.5 justify-center max-w-xs mx-auto">
                                                    @forelse (($praktikan->modul_belum_asistensi ?? collect()) as $modul)
                                                        <div class="rounded-xl bg-amber-50 border border-amber-200/80 px-2.5 py-1.5 text-xs text-center">
                                                            <p class="font-extrabold text-amber-800">
                                                                Pertemuan {{ $modul['pertemuan'] }}
                                                            </p>
                                                            <p class="text-[11px] text-slate-500 truncate max-w-[140px]" title="{{ $modul['nama_modul'] ?? '-' }}">
                                                                {{ $modul['nama_modul'] ?? '-' }}
                                                            </p>
                                                        </div>
                                                    @empty
                                                        <span class="text-slate-400">-</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                            <td class="p-3.5 text-center">
                                                <a href="{{ route('asisten.penilaian.show', $praktikan) }}"
                                                   class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-blue-700 text-white text-xs font-extrabold hover:bg-blue-800 transition shadow-xs cursor-pointer">
                                                    <span>Lihat</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="p-6 text-center text-slate-500">
                                                Tidak ada data praktikan pada program studi ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl font-black mx-auto mb-3">
                    ✓
                </div>
                <h4 class="text-base font-extrabold text-slate-900">
                    Semua Asistensi Praktikum Selesai!
                </h4>
                <p class="text-sm text-slate-500 mt-1">
                    Semua praktikan telah menyelesaikan seluruh asistensi modul praktikum.
                </p>
            </div>
        @endif
    </section>

</div>
@endsection