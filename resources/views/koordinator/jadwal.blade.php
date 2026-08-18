@extends('layouts.koordinator', ['title' => 'Jadwal Praktikum'])

@section('content')
<div class="space-y-8 max-w-full overflow-x-hidden"
     x-data="{
        deleteModal: false,
        deleteUrl: '',
        deleteTitle: '',
        openDelete(url, title) {
            this.deleteUrl = url;
            this.deleteTitle = title;
            this.deleteModal = true;
        },
        closeDelete() {
            this.deleteModal = false;
            this.deleteUrl = '';
            this.deleteTitle = '';
        }
     }">

    {{-- Header Section --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Jadwal Praktikum & Laporan
                </h1>

                <p class="text-slate-500 mt-2 max-w-3xl leading-relaxed">
                    Kelola jadwal pengambilan/pengumpulan laporan serta plotting jadwal modul dan sesi praktikum.
                    Sistem otomatis memverifikasi agar tidak terjadi tabrakan jadwal kelompok maupun sesi pada jam yang sama.
                </p>
            </div>

            {{-- KPI Summary Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 shrink-0">
                <div class="rounded-2xl bg-blue-50 border border-blue-100 p-3.5 text-center">
                    <p class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Jadwal Modul</p>
                    <p class="text-2xl font-black text-blue-900 mt-0.5">{{ $totalJadwalModul ?? 0 }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-3.5 text-center">
                    <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Jadwal Laporan</p>
                    <p class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalJadwalLaporan ?? 0 }}</p>
                </div>

                <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-3.5 text-center col-span-2 sm:col-span-1">
                    <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Status Validasi</p>
                    <p class="text-xs font-black text-emerald-800 mt-2 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 text-emerald-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Bebas Tabrakan Jadwal
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Status Sukses --}}
    @if (session('status'))
        <section class="bg-emerald-50 border border-emerald-200 rounded-3xl p-5 text-emerald-800 font-bold flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 text-base font-black">
                ✓
            </div>
            <div>
                {{ session('status') }}
            </div>
        </section>
    @endif

    {{-- Error Handling Alert (Khusus Tabrakan Jadwal dan Validasi) --}}
    @if (isset($errors) && $errors->any())
        <section class="bg-red-50 border-2 border-red-200 rounded-3xl p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 text-xl font-bold">
                    ⚠️
                </div>
                <div class="space-y-2 flex-1">
                    <h2 class="text-lg font-black text-red-900">
                        @if ($errors->has('bentrok'))
                            Peringatan Tabrakan Jadwal Terdeteksi!
                        @else
                            Terjadi Kesalahan Pengisian Data
                        @endif
                    </h2>

                    @if ($errors->has('bentrok'))
                        <div class="rounded-2xl bg-white border border-red-200 p-4 text-red-800 font-bold text-sm shadow-xs leading-relaxed">
                            {{ $errors->first('bentrok') }}
                        </div>
                        <p class="text-xs text-red-600 font-medium">
                            * Sistem mencegah penyimpanan jadwal ini untuk memastikan tidak ada kelompok, modul, atau sesi praktikum yang bertabrakan waktu.
                        </p>
                    @endif

                    @if ($errors->count() > ($errors->has('bentrok') ? 1 : 0))
                        <ul class="list-disc list-inside text-sm text-red-700 font-medium space-y-1">
                            @foreach ($errors->all() as $error)
                                @if ($error !== $errors->first('bentrok'))
                                    <li>{{ $error }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Filter Bar --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET"
              action="{{ route('koordinator.jadwal') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Periode Praktikum
                </label>

                <select name="praktikum_id"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-medium">
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }} {{ ucfirst($praktikum->semester) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>

                <select name="prodi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-medium">
                    <option value="">Semua Prodi</option>

                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected($selectedProdi === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition shadow-sm">
                    Tampilkan
                </button>

                <a href="{{ route('koordinator.jadwal') }}"
                   class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>

    {{-- Form Tambah Jadwal Laporan --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">
                    Tambah Jadwal Pengambilan/Pengumpulan Laporan
                </h2>
                <p class="text-slate-500 text-sm mt-1">
                    Atur hari dan rentang jam pengumpulan laporan per program studi.
                </p>
            </div>
        </div>

        <form method="POST"
              action="{{ route('koordinator.jadwal.laporan.store') }}"
              class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-6">
            @csrf

            <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi <span class="text-red-500">*</span>
                </label>

                <select name="prodi"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Prodi</option>

                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected(old('prodi', $selectedProdi) === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Hari <span class="text-red-500">*</span>
                </label>

                <select name="hari"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($hariOptions as $hari)
                        <option value="{{ $hari }}" @selected(old('hari') === $hari)>
                            {{ $hari }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jam Mulai <span class="text-red-500">*</span>
                </label>

                <input type="time"
                       name="jam_mulai"
                       value="{{ old('jam_mulai') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jam Selesai <span class="text-red-500">*</span>
                </label>

                <input type="time"
                       name="jam_selesai"
                       value="{{ old('jam_selesai') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Keterangan
                </label>

                <input type="text"
                       name="keterangan"
                       value="{{ old('keterangan') }}"
                       placeholder="Contoh: Lab Fisdas"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-6 flex justify-end">
                <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition shadow-sm">
                    Simpan Jadwal Laporan
                </button>
            </div>
        </form>
    </section>

    {{-- Tabel Jadwal Laporan --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100 flex items-center justify-between">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Daftar Jadwal Pengambilan/Pengumpulan Laporan
            </h2>
            <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                Total: {{ $jadwalLaporans->count() }} Jadwal
            </span>
        </div>

        <div class="max-w-full overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-4 py-3.5 text-center font-extrabold">Prodi</th>
                        <th class="px-4 py-3.5 text-center font-extrabold">Hari</th>
                        <th class="px-4 py-3.5 text-center font-extrabold">Waktu</th>
                        <th class="px-4 py-3.5 text-center font-extrabold">Keterangan</th>
                        <th class="px-4 py-3.5 text-center font-extrabold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-blue-50">
                    @forelse ($jadwalLaporans as $jadwal)
                        <tr class="hover:bg-blue-50/30 transition">
                            <form method="POST" action="{{ route('koordinator.jadwal.laporan.update', $jadwal) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">

                                <td class="px-4 py-3 font-semibold">
                                    <select name="prodi"
                                            class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                        @foreach ($prodis as $prodi)
                                            <option value="{{ $prodi }}" @selected($jadwal->prodi === $prodi)>
                                                {{ $prodi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-4 py-3">
                                    <select name="hari"
                                            class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                        @foreach ($hariOptions as $hari)
                                            <option value="{{ $hari }}" @selected($jadwal->hari === $hari)>
                                                {{ $hari }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <input type="time"
                                               name="jam_mulai"
                                               value="{{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '' }}"
                                               class="rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                        <span class="text-slate-400 font-bold">-</span>
                                        <input type="time"
                                               name="jam_selesai"
                                               value="{{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '' }}"
                                               class="rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <input type="text"
                                           name="keterangan"
                                           value="{{ $jadwal->keterangan }}"
                                           placeholder="Keterangan..."
                                           class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                </td>

                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="submit"
                                                class="px-3.5 py-2 rounded-xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition shadow-xs">
                                            Simpan
                                        </button>

                                        <button type="button"
                                                @click="openDelete('{{ route('koordinator.jadwal.laporan.delete', $jadwal) }}', 'jadwal laporan {{ $jadwal->prodi }} ({{ $jadwal->hari }})')"
                                                class="px-3.5 py-2 rounded-xl bg-red-600 text-white font-extrabold hover:bg-red-700 transition shadow-xs">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500 font-medium">
                                Belum ada jadwal laporan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Form Tambah Jadwal Modul dan Sesi (dengan Alpine.js Dependent Dropdown) --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6"
             x-data="{
                selectedProdiForm: '{{ old('prodi', $selectedProdi ?? ($prodis->first() ?? '')) }}',
                allKelompoks: {{ Js::from($kelompoks) }},
                get filteredKelompoks() {
                    if (!this.selectedProdiForm) return this.allKelompoks;
                    return this.allKelompoks.filter(k => k.prodi === this.selectedProdiForm);
                }
             }">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Tambah Jadwal Modul dan Sesi Praktikum
            </h2>

            <p class="text-slate-500 text-sm mt-1">
                Pilih prodi untuk memfilter daftar kelompok secara otomatis, lalu tentukan minggu, jam, dan modul praktikum.
            </p>
        </div>

        <form method="POST"
              action="{{ route('koordinator.jadwal.modul.store') }}"
              class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-6">
            @csrf

            <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi <span class="text-red-500">*</span>
                </label>

                <select name="prodi"
                        x-model="selectedProdiForm"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-medium">
                    <option value="">Pilih Prodi</option>

                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}">
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Minggu Ke <span class="text-red-500">*</span>
                </label>

                <select name="minggu_ke"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-800">
                    @foreach ([1, 2, 3, 4] as $m)
                        <option value="{{ $m }}" @selected(old('minggu_ke', 1) == $m)>
                            Minggu {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Hari <span class="text-red-500">*</span>
                </label>

                <select name="hari"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($hariOptions as $hari)
                        <option value="{{ $hari }}" @selected(old('hari') === $hari)>
                            {{ $hari }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jam Mulai <span class="text-red-500">*</span>
                </label>

                <input type="time"
                       name="jam_mulai"
                       value="{{ old('jam_mulai') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jam Selesai <span class="text-red-500">*</span>
                </label>

                <input type="time"
                       name="jam_selesai"
                       value="{{ old('jam_selesai') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Modul Praktikum <span class="text-red-500">*</span>
                </label>

                <select name="modul_id"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Modul</option>

                    @foreach ($moduls as $modul)
                        <option value="{{ $modul->id }}" @selected(old('modul_id') == $modul->id)>
                            Modul {{ $modul->nomor_modul }} - {{ $modul->nama_modul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Kelompok <span class="text-red-500">*</span>
                </label>

                <select name="kelompok_id"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-semibold text-blue-950">
                    <option value="">Pilih Kelompok</option>

                    <template x-for="k in filteredKelompoks" :key="k.id">
                        <option :value="k.id" x-text="k.nama_kelompok + (k.sesi ? ' (' + k.sesi + ')' : '') + ' - ' + k.prodi"></option>
                    </template>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Keterangan
                </label>

                <input type="text"
                       name="keterangan"
                       value="{{ old('keterangan') }}"
                       placeholder="Opsional (misal: Sesi Tambahan)"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-6 flex justify-end">
                <button type="submit"
                        class="px-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition shadow-sm">
                    Simpan Jadwal Modul
                </button>
            </div>
        </form>
    </section>

    {{-- Tabel Jadwal Modul dan Sesi Praktikum Grouped By Prodi & Minggu --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden"
             x-data="{
                activeProdiTab: '{{ $selectedProdi && $jadwalModulByProdi->has($selectedProdi) ? $selectedProdi : ($jadwalModulByProdi->keys()->first() ?? '') }}'
             }">
        <div class="p-6 border-b border-blue-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">
                    Jadwal Modul dan Sesi Praktikum
                </h2>

                <p class="text-slate-500 text-sm mt-1">
                    Daftar plotting modul per program studi dan minggu praktikum.
                </p>
            </div>

            <span class="text-xs font-bold px-3.5 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100 self-start sm:self-auto">
                Total: {{ $jadwalModulPraktikums->count() }} Sesi
            </span>
        </div>

        @if ($jadwalModulByProdi->isNotEmpty())
            {{-- Tab Bar Navigasi Prodi --}}
            <div class="bg-slate-50 border-b border-slate-200/80 px-6 py-3.5">
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider shrink-0 mr-2 flex items-center gap-1.5">
                        <span>🏛️</span>
                        <span>Program Studi:</span>
                    </span>

                    @foreach ($jadwalModulByProdi as $prodi => $mingguRows)
                        <button type="button"
                                @click="activeProdiTab = '{{ $prodi }}'"
                                :class="activeProdiTab === '{{ $prodi }}'
                                    ? 'bg-blue-700 text-white shadow-md'
                                    : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200 hover:border-blue-200'"
                                class="px-4 py-2 rounded-2xl text-xs font-extrabold transition-all duration-150 shrink-0 flex items-center gap-2 cursor-pointer">
                            <span>{{ $prodi }}</span>
                            <span :class="activeProdiTab === '{{ $prodi }}' ? 'bg-blue-800/80 text-blue-100' : 'bg-slate-100 text-slate-600'"
                                  class="px-2 py-0.5 rounded-lg text-[10px] font-black">
                                {{ $mingguRows->flatten()->count() }} Sesi
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="p-6 space-y-6 max-w-full overflow-x-hidden">
            @forelse ($jadwalModulByProdi as $prodi => $mingguRows)
                <div x-cloak
                     x-show="activeProdiTab === '{{ $prodi }}'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="rounded-3xl border border-blue-100 overflow-hidden shadow-xs"
                     x-data="{
                        activeMinggu: {{ $mingguRows->keys()->first() ?? 1 }}
                     }">
                    {{-- Header Prodi --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-700 to-indigo-700 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-black tracking-wide flex items-center gap-2">
                                <span>🏛️</span>
                                <span>{{ $prodi }}</span>
                            </h3>
                            <p class="text-xs text-blue-100 mt-0.5">
                                {{ $mingguRows->count() }} Minggu Praktikum Terjadwal
                            </p>
                        </div>

                        <span class="text-xs font-bold bg-white/20 px-3.5 py-1.5 rounded-full self-start sm:self-auto backdrop-blur-xs">
                            Total {{ $mingguRows->flatten()->count() }} Sesi
                        </span>
                    </div>

                    {{-- Tab Bar Navigasi Minggu --}}
                    <div class="bg-slate-100 border-b border-slate-200 px-6 py-3 flex items-center gap-2 overflow-x-auto">
                        <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider shrink-0 mr-1">
                            Pilih Minggu:
                        </span>

                        @foreach ($mingguRows->keys() as $mKey)
                            <button type="button"
                                    @click="activeMinggu = {{ $mKey }}"
                                    :class="activeMinggu === {{ $mKey }} ? 'bg-blue-700 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-blue-50 border border-slate-200 hover:border-blue-200'"
                                    class="px-4 py-2 rounded-2xl text-xs font-extrabold transition-all duration-150 shrink-0 flex items-center gap-2 cursor-pointer">
                                <span>📅 Minggu {{ $mKey }}</span>
                                <span :class="activeMinggu === {{ $mKey }} ? 'bg-blue-800/80 text-blue-100' : 'bg-slate-100 text-slate-600'"
                                      class="px-2 py-0.5 rounded-lg text-[10px] font-black">
                                    {{ $mingguRows[$mKey]->count() }} Sesi
                                </span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Content Tabs per Minggu --}}
                    @foreach ($mingguRows as $mingguKe => $rows)
                        <div x-show="activeMinggu === {{ $mingguKe }}"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="border-b border-blue-50 last:border-b-0">
                            <div class="px-6 py-3.5 bg-blue-50/40 border-b border-blue-100/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-xl bg-blue-600 text-white font-extrabold text-xs">
                                        Minggu {{ $mingguKe }}
                                    </span>
                                    <span class="font-extrabold text-slate-800 text-xs">
                                        Jadwal Praktikum {{ $prodi }}
                                    </span>
                                </div>

                                <span class="text-xs font-bold text-slate-600 bg-white px-3 py-1 rounded-full border border-slate-200">
                                    Hari: <span class="text-blue-700 font-extrabold">{{ $rows->first()?->hari ?? 'Senin' }}</span>
                                </span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-slate-100/70 text-slate-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-extrabold">Hari</th>
                                            <th class="px-4 py-3 text-left font-extrabold">Waktu</th>
                                            <th class="px-4 py-3 text-left font-extrabold">Modul</th>
                                            <th class="px-4 py-3 text-left font-extrabold">Kelompok & Sesi</th>
                                            <th class="px-4 py-3 text-left font-extrabold">Keterangan</th>
                                            <th class="px-4 py-3 text-right font-extrabold">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-blue-50">
                                        @foreach ($rows as $jadwal)
                                            <tr class="hover:bg-blue-50/30 transition">
                                                <form method="POST" action="{{ route('koordinator.jadwal.modul.update', $jadwal) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">
                                                    <input type="hidden" name="prodi" value="{{ $jadwal->prodi }}">
                                                    <input type="hidden" name="minggu_ke" value="{{ $jadwal->minggu_ke }}">

                                                    <td class="px-4 py-3">
                                                        <select name="hari"
                                                                class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500 font-semibold text-slate-800">
                                                            @foreach ($hariOptions as $hari)
                                                                <option value="{{ $hari }}" @selected(($jadwal->hari ?? 'Senin') === $hari)>
                                                                    {{ $hari }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center gap-1.5">
                                                            <input type="time"
                                                                   name="jam_mulai"
                                                                   value="{{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '' }}"
                                                                   class="rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                                            <span class="text-slate-400 font-bold">-</span>
                                                            <input type="time"
                                                                   name="jam_selesai"
                                                                   value="{{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '' }}"
                                                                   class="rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                                        </div>
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        <select name="modul_id"
                                                                class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500 font-medium">
                                                            @foreach ($moduls as $modul)
                                                                <option value="{{ $modul->id }}" @selected($jadwal->modul_id == $modul->id)>
                                                                    Modul {{ $modul->nomor_modul }} - {{ $modul->nama_modul }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        <select name="kelompok_id"
                                                                class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500 font-bold text-slate-800">
                                                            @foreach ($kelompoks->where('prodi', $jadwal->prodi) as $kelompok)
                                                                <option value="{{ $kelompok->id }}" @selected($jadwal->kelompok_id == $kelompok->id)>
                                                                    {{ $kelompok->nama_kelompok }} {{ $kelompok->sesi ? "({$kelompok->sesi})" : '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        <input type="text"
                                                               name="keterangan"
                                                               value="{{ $jadwal->keterangan }}"
                                                               placeholder="Keterangan..."
                                                               class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                                    </td>

                                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                                        <div class="flex items-center justify-end gap-2">
                                                            <button type="submit"
                                                                    class="px-3.5 py-2 rounded-xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition shadow-xs">
                                                                Simpan
                                                            </button>

                                                            <button type="button"
                                                                    @click="openDelete('{{ route('koordinator.jadwal.modul.delete', $jadwal) }}', 'jadwal {{ $jadwal->prodi }} M{{ $jadwal->minggu_ke }} - {{ $jadwal->kelompok_label }}')"
                                                                    class="px-3.5 py-2 rounded-xl bg-red-600 text-white font-extrabold hover:bg-red-700 transition shadow-xs">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </td>
                                                </form>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="rounded-3xl border border-blue-100 p-12 text-center text-slate-500">
                    <p class="text-base font-bold">Jadwal modul dan sesi belum tersedia untuk periode atau filter ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Silakan gunakan form di atas untuk menambahkan jadwal praktikum baru.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-cloak
         x-show="deleteModal"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
        <div @click.outside="closeDelete()"
             x-transition
             class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-red-100 overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-3xl mb-4">
                    ⚠️
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900">
                    Hapus Jadwal?
                </h2>

                <p class="text-slate-500 mt-3 leading-relaxed text-sm">
                    Data <span class="font-bold text-slate-800" x-text="deleteTitle"></span> akan dihapus dari sistem.
                </p>

                <p class="text-xs text-red-500 mt-2 font-semibold">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="px-6 pb-6 grid grid-cols-2 gap-3">
                <button type="button"
                        @click="closeDelete()"
                        class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Batal
                </button>

                <form method="POST" x-bind:action="deleteUrl">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="w-full px-5 py-3 rounded-2xl bg-red-600 text-white font-extrabold hover:bg-red-700 transition shadow-sm">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection