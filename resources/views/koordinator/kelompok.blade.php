@extends('layouts.koordinator', ['title' => 'Kelompok Praktikum'])

@section('content')
<div class="space-y-8 max-w-full overflow-x-hidden">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Kelompok Praktikum
        </h1>

        <p class="text-slate-500 mt-2">
            Kelola kelompok praktikum berdasarkan program studi, sesi, dan asisten pemeriksa per modul.
        </p>
    </section>

    @if (isset($errors) && $errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3">
                Terjadi kesalahan input:
            </h2>

            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">
            Tambah Kelompok
        </h2>

        <form method="POST" action="{{ route('koordinator.kelompok.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div class="min-w-0">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Periode Praktikum
                </label>

                <select name="praktikum_id"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Periode</option>

                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected(old('praktikum_id', $praktikumId) == $praktikum->id)>
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }} {{ $praktikum->semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>

                <input type="text"
                       name="prodi"
                       value="{{ old('prodi') }}"
                       list="list-prodi"
                       required
                       placeholder="Contoh: Teknik Industri"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <datalist id="list-prodi">
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}"></option>
                    @endforeach
                </datalist>
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nama Kelompok
                </label>

                <input type="text"
                       name="nama_kelompok"
                       value="{{ old('nama_kelompok') }}"
                       required
                       placeholder="Contoh: Kelompok 1"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Sesi Praktikum
                </label>

                <input type="text"
                       name="sesi"
                       value="{{ old('sesi') }}"
                       placeholder="Contoh: Sesi 1"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    NIM Awal
                </label>

                <input type="text"
                       name="nim_awal"
                       value="{{ old('nim_awal') }}"
                       placeholder="2209106001"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-slate-500 mt-2">
                    Masukkan NIM lengkap.
                </p>
            </div>

            <div class="min-w-0">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    NIM Akhir
                </label>

                <input type="text"
                       name="nim_akhir"
                       value="{{ old('nim_akhir') }}"
                       placeholder="2209106005"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-slate-500 mt-2">
                    Awalan NIM harus sama. Sistem mengambil rentang dari 3 angka terakhir.
                </p>
            </div>

            <div class="md:col-span-2 rounded-2xl bg-blue-50 border border-blue-100 p-4 text-sm text-blue-700 leading-relaxed">
                Contoh: isi NIM Awal <b>2209106001</b> dan NIM Akhir <b>2209106005</b>. Sistem akan memasukkan praktikan dengan awalan NIM yang sama dan nomor urut 001 sampai 005.
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                        class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Tambah Kelompok
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        {{-- Filter + Pencarian --}}
        <div class="p-6 border-b border-blue-100">
            <form method="GET"
                action="{{ route('koordinator.kelompok') }}"
                class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">

                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari NIM, nama, kelompok"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <select name="praktikum_id"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                            {{ $praktikum->nama }}
                        </option>
                    @endforeach
                </select>

                <select name="prodi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Prodi</option>

                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>

                <select name="sesi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Sesi</option>

                    @foreach ($sesis as $sesi)
                        <option value="{{ $sesi }}" @selected(request('sesi') === $sesi)>
                            {{ $sesi }}
                        </option>
                    @endforeach
                </select>

                <select name="per_page"
                        onchange="this.form.submit()"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 font-semibold text-slate-700">
                    <option value="10" @selected(($perPage ?? 10) == 10)>Tampilkan 10 Isi</option>
                    <option value="50" @selected(($perPage ?? 10) == 50)>Tampilkan 50 Isi</option>
                    <option value="100" @selected(($perPage ?? 10) == 100)>Tampilkan 100 Isi</option>
                </select>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit"
                            class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                        Cari
                    </button>

                    <a href="{{ route('koordinator.kelompok') }}"
                    class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Judul Tabel & Info Jumlah --}}
        <div class="p-6 border-b border-blue-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-center md:text-left">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">
                    Kelompok Praktikum Fisika Dasar {{ request('prodi') ? ': ' . request('prodi') : '' }}
                </h2>

                <p class="text-slate-500 mt-1">
                    Laboratorium Layanan Dasar
                </p>
            </div>

            <div class="flex items-center justify-center md:justify-end gap-2">
                <span class="text-xs font-extrabold px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                    Menampilkan {{ $praktikanRows->firstItem() ?? 0 }} - {{ $praktikanRows->lastItem() ?? 0 }} dari {{ $praktikanRows->total() }} Praktikan
                </span>
            </div>
        </div>

        {{-- Tabel Desktop --}}
        <div class="p-6">
            <div class="hidden md:block">
                <table class="w-full table-fixed text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="w-[60px] p-3 text-center rounded-l-xl">No.</th>
                            <th class="w-[150px] p-3 text-center">NIM</th>
                            <th class="p-3 text-center">Nama</th>
                            <th class="w-[150px] p-3 text-center">Kelompok</th>
                            <th class="w-[130px] p-3 text-center">Sesi</th>
                            <th class="w-[120px] p-3 text-center rounded-r-xl">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($praktikanRows as $praktikan)
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="p-3 text-center">
                                    {{ ($praktikanRows->currentPage() - 1) * $praktikanRows->perPage() + $loop->iteration }}
                                </td>

                                <td class="p-3 text-center break-words">
                                    {{ $praktikan->user->nim ?? '-' }}
                                </td>

                                <td class="p-3 font-semibold text-slate-900 break-words">
                                    {{ $praktikan->user->name ?? '-' }}
                                </td>

                                <td class="p-3 text-center font-bold text-slate-900 break-words">
                                    {{ $praktikan->kelompok->nama_kelompok ?? '-' }}
                                </td>

                                <td class="p-3 text-center break-words">
                                    {{ $praktikan->kelompok->sesi ?? '-' }}
                                </td>

                                <td class="p-3 text-center">
                                    @if ($praktikan->kelompok)
                                        <a href="{{ route('koordinator.kelompok.show', $praktikan->kelompok) }}"
                                        class="inline-flex px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition text-xs">
                                            Detail
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-5 text-center text-slate-500">
                                    Data praktikan pada filter ini belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tampilan Mobile --}}
            <div class="md:hidden space-y-4">
                @forelse ($praktikanRows as $praktikan)
                    <div class="rounded-2xl border border-blue-100 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-extrabold text-slate-900">
                                    {{ $praktikan->user->name ?? '-' }}
                                </p>

                                <p class="text-sm text-slate-500 mt-1">
                                    {{ $praktikan->user->nim ?? '-' }}
                                </p>
                            </div>

                            <span class="shrink-0 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                {{ $praktikan->kelompok->sesi ?? '-' }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-slate-500">Kelompok</p>
                                <p class="font-bold text-slate-900 mt-1">
                                    {{ $praktikan->kelompok->nama_kelompok ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-slate-500">Prodi</p>
                                <p class="font-bold text-slate-900 mt-1">
                                    {{ $praktikan->user->prodi ?? '-' }}
                                </p>
                            </div>
                        </div>

                        @if ($praktikan->kelompok)
                            <a href="{{ route('koordinator.kelompok.show', $praktikan->kelompok) }}"
                            class="mt-4 inline-flex w-full justify-center px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition text-sm">
                                Detail Kelompok
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center text-slate-500">
                        Data praktikan pada filter ini belum tersedia.
                    </div>
                @endforelse
            </div>

            @if ($praktikanRows->hasPages())
                <div class="mt-6 pt-4 border-t border-slate-100">
                    {{ $praktikanRows->links() }}
                </div>
            @endif
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden max-w-full">
        <div class="p-6 border-b border-blue-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">
                        Asisten Pemeriksa Laporan Praktikum
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Pilih program studi untuk melihat pembagian asisten pemeriksa setiap modul.
                    </p>
                </div>

                <form method="GET"
                    action="{{ route('koordinator.kelompok') }}"
                    class="flex flex-col sm:flex-row gap-3">

                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">
                    <input type="hidden" name="sesi" value="{{ request('sesi') }}">

                    <select name="prodi"
                            onchange="this.form.submit()"
                            class="w-full sm:w-[280px] rounded-2xl border border-blue-200 bg-white text-slate-800 font-semibold focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Program Studi</option>

                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>
                                {{ $prodi }}
                            </option>
                        @endforeach
                    </select>

                    <a href="{{ route('koordinator.kelompok', ['praktikum_id' => $praktikumId]) }}"
                    class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                        Reset
                    </a>
                </form>
            </div>
        </div>

        <div class="p-6 max-w-full overflow-hidden">
            <div class="mb-5 text-center">
                <h3 class="text-xl font-extrabold text-slate-900">
                    Asisten Pemeriksa Laporan Praktikum Fisika Dasar
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Laboratorium Layanan Dasar
                </p>

                <p class="text-base font-bold text-blue-700 mt-1">
                    Program Studi: {{ request('prodi') ?: 'Semua Program Studi' }}
                </p>
            </div>

            <div class="w-full rounded-2xl border border-blue-100 overflow-hidden shadow-xs">
                <table class="w-full text-xs xl:text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 border-b border-blue-100">
                            <th class="w-[70px] px-4 py-3.5 text-center font-extrabold">No.</th>
                            <th class="w-[160px] px-4 py-3.5 text-left font-extrabold">Modul</th>
                            <th class="px-4 py-3.5 text-left font-extrabold">Nama Modul Praktikum</th>
                            <th class="w-[320px] px-4 py-3.5 text-left font-extrabold">Asisten Pemeriksa Laporan</th>
                            <th class="w-[120px] px-4 py-3.5 text-center font-extrabold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-blue-50">
                        @forelse ($moduls as $modul)
                            @php
                                $selectedAsisten = $asistenPemeriksaMap[$modul->id] ?? null;
                            @endphp

                            <tr class="hover:bg-blue-50/40 transition">
                                <form method="POST" action="{{ route('koordinator.kelompok.asisten-pemeriksa.store') }}">
                                    @csrf
                                    <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">
                                    <input type="hidden" name="prodi" value="{{ request('prodi') }}">
                                    <input type="hidden" name="modul_id" value="{{ $modul->id }}">

                                    <td class="px-4 py-3.5 text-center font-bold text-slate-500">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="px-4 py-3.5 font-black text-blue-900 whitespace-nowrap">
                                        Modul {{ $modul->nomor_modul }}
                                    </td>

                                    <td class="px-4 py-3.5 font-semibold text-slate-800">
                                        {{ $modul->nama_modul }}
                                    </td>

                                    <td class="px-4 py-3.5">
                                        <select name="asisten_id"
                                                class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500 font-semibold text-slate-800">
                                            <option value="">-- Pilih Asisten Pemeriksa --</option>
                                            @foreach ($asistensAktif as $asisten)
                                                <option value="{{ $asisten->id }}" @selected($selectedAsisten == $asisten->id)>
                                                    {{ $asisten->user->name ?? '-' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <button type="submit"
                                                class="px-4 py-2 rounded-xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition text-xs shadow-xs">
                                            Simpan
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-500">
                                    Data modul praktikum belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($asistensAktif->isEmpty())
                <div class="mt-5 rounded-2xl bg-yellow-50 border border-yellow-100 p-4 text-yellow-700 font-semibold">
                    Belum ada asisten aktif pada periode ini. Tambahkan asisten aktif terlebih dahulu di menu Data Asisten.
                </div>
            @endif
        </div>
    </section>

</div>
@endsection