@extends('layouts.koordinator', ['title' => 'Detail Kelompok'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">
                    Detail Kelompok
                </h1>

                <p class="text-slate-500 mt-2">
                    {{ $kelompok->praktikum->nama ?? '-' }} · {{ $kelompok->prodi ?? '-' }} · {{ $kelompok->sesi ?? '-' }}
                </p>
            </div>

            <a href="{{ route('koordinator.kelompok', [
                    'praktikum_id' => $kelompok->praktikum_id,
                    'prodi' => $kelompok->prodi,
                    'sesi' => $kelompok->sesi,
                ]) }}"
               class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                Kembali
            </a>
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

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">
            Data Kelompok
        </h2>

        <form method="POST" action="{{ route('koordinator.kelompok.update', $kelompok) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nama Kelompok
                </label>

                <input type="text"
                       name="nama_kelompok"
                       value="{{ old('nama_kelompok', $kelompok->nama_kelompok) }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>

                <input type="text"
                       name="prodi"
                       value="{{ old('prodi', $kelompok->prodi) }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Sesi Praktikum
                </label>

                <input type="text"
                       name="sesi"
                       value="{{ old('sesi', $kelompok->sesi) }}"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div></div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Tambah Praktikan dari NIM Awal
                </label>

                <input type="text"
                       name="nim_awal"
                       value="{{ old('nim_awal') }}"
                       placeholder="2209106001"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-slate-500 mt-2">
                    Masukkan NIM lengkap, bukan hanya nomor urut.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Sampai NIM Akhir
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

            <div class="md:col-span-2 rounded-2xl bg-blue-50 border border-blue-100 p-4 text-sm text-blue-700">
                Contoh: isi NIM Awal <b>2209106001</b> dan NIM Akhir <b>2209106005</b>. Sistem akan memasukkan praktikan dengan awalan NIM yang sama dan nomor urut 001 sampai 005.
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">
                        Asisten Pemeriksa per Modul
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Pilih asisten pemeriksa untuk setiap modul, lalu klik tombol simpan.
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('koordinator.kelompok.asisten-pemeriksa.store') }}">
            @csrf

            <input type="hidden" name="praktikum_id" value="{{ $kelompok->praktikum_id }}">
            <input type="hidden" name="kelompok_id" value="{{ $kelompok->id }}">

            <div class="p-6 overflow-x-auto">
                <table class="w-full min-w-[700px] text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="p-3 text-center rounded-l-xl">
                                Modul
                            </th>

                            <th class="p-3 text-center">
                                Nama Modul
                            </th>

                            <th class="p-3 text-center rounded-r-xl">
                                Asisten Pemeriksa
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($moduls as $modul)
                            @php
                                $selectedAsisten = $asistenPemeriksaMap[$modul->id] ?? null;
                            @endphp

                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="p-3 text-center font-extrabold text-slate-900">
                                    Modul {{ $loop->iteration }}
                                </td>

                                <td class="p-3 text-center text-slate-700">
                                    {{ $modul->nama_modul }}
                                </td>

                                <td class="p-3">
                                    <select name="asisten_pemeriksa[{{ $modul->id }}]"
                                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Belum dipilih</option>

                                        @foreach ($asistensAktif as $asisten)
                                            <option value="{{ $asisten->id }}" @selected($selectedAsisten == $asisten->id)>
                                                {{ $asisten->user->name ?? '-' }} - {{ $asisten->user->email ?? '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-5 text-center text-slate-500">
                                    Modul belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($asistensAktif->isEmpty())
                    <div class="mt-5 rounded-2xl bg-yellow-50 border border-yellow-100 p-4 text-yellow-700 font-semibold">
                        Belum ada asisten aktif pada periode ini. Tambahkan asisten aktif terlebih dahulu di menu Data Asisten.
                    </div>
                @endif

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                        Simpan Asisten Pemeriksa
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- Anggota Kelompok --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Anggota Kelompok
            </h2>

            <p class="text-slate-500 mt-1">
                Data praktikan yang tergabung dalam kelompok ini.
            </p>
        </div>

        <div class="p-6">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full min-w-[1000px] text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="p-3 text-center rounded-l-xl">No.</th>
                            <th class="p-3 text-center">NIM</th>
                            <th class="p-3 text-center">Nama</th>
                            <th class="p-3 text-center">Email</th>
                            <th class="p-3 text-center">Nomor Ponsel</th>
                            <th class="p-3 text-center">Program Studi</th>
                            <th class="p-3 text-center rounded-r-xl">Jenis Kelamin</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kelompok->praktikans->sortBy('user.nim')->values() as $praktikan)
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="p-3 text-center">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="p-3 text-center whitespace-nowrap font-semibold">
                                    {{ $praktikan->user->nim ?? '-' }}
                                </td>

                                <td class="p-3 text-center font-bold text-slate-900">
                                    {{ $praktikan->user->name ?? '-' }}
                                </td>

                                <td class="p-3 text-center whitespace-nowrap">
                                    {{ $praktikan->user->email ?? '-' }}
                                </td>

                                <td class="p-3 text-center whitespace-nowrap">
                                    {{ $praktikan->user->nomor_ponsel ?? '-' }}
                                </td>

                                <td class="p-3 text-center">
                                    {{ $praktikan->user->prodi ?? '-' }}
                                </td>

                                <td class="p-3 text-center">
                                    {{ match ($praktikan->user->jenis_kelamin ?? null) {
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                        default => '-',
                                    } }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-5 text-center text-slate-500">
                                    Belum ada praktikan pada kelompok ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

</div>
@endsection