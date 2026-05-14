@extends('layouts.praktikan', ['title' => 'Jadwal Praktikan'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Jadwal Praktikan
        </h1>

        <p class="text-slate-500 mt-2">
            Halaman ini menampilkan jadwal yang berkaitan dengan praktikan berdasarkan program studi,
            kelompok, dan sesi praktikum.
        </p>
    </section>

    <!-- Jadwal pribadi -->
    <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Program Studi</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->user->prodi ?? '-' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Kelompok</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->kelompok->nama_kelompok ?? 'Belum ditentukan' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Sesi</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->kelompok->sesi ?? 'Belum ditentukan' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Asisten</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $asisten->user->name ?? 'Belum ditentukan' }}
            </h3>
        </div>
    </section>

    <!-- Pembagian Modul dan Sesi Praktikum -->
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Pembagian Modul dan Sesi Praktikum
            </h2>
            <p class="text-slate-500 mt-1">
                Modul dan sesi praktikum disesuaikan berdasarkan program studi.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            @if (isset($jadwalModulSesi) && count($jadwalModulSesi) > 0)
                <table class="w-full min-w-[1100px] text-sm border border-slate-200">
                    <thead>
                        <tr class="bg-orange-300 text-slate-900">
                            <th class="border border-slate-300 p-3 text-center font-extrabold">
                                Waktu
                            </th>

                            @foreach ($moduls as $modul)
                                <th class="border border-slate-300 p-3 text-center font-extrabold whitespace-nowrap">
                                    {{ $modul->nama_modul }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($jadwalModulSesi as $minggu => $jadwals)
                            <tr>
                                <td colspan="{{ $moduls->count() + 1 }}"
                                    class="border border-slate-300 bg-slate-50 p-2 text-center font-extrabold">
                                    {{ $minggu }}
                                </td>
                            </tr>

                            @foreach ($jadwals as $jadwal)
                                <tr>
                                    <td class="border border-slate-300 bg-orange-200 p-3 text-center font-bold whitespace-nowrap">
                                        {{ $jadwal->waktu ?? '-' }}
                                    </td>

                                    @foreach ($moduls as $modul)
                                        <td class="border border-slate-300 p-3 text-center whitespace-nowrap">
                                            {{ $jadwal->{'modul_' . $modul->urutan} ?? '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center">
                    <div class="text-4xl mb-3">📋</div>

                    <h3 class="text-xl font-extrabold text-slate-900">
                        Jadwal modul dan sesi praktikum belum tersedia
                    </h3>

                    <p class="text-slate-500 mt-2">
                        Data pembagian modul dan sesi praktikum akan ditampilkan setelah diinput oleh koordinator.
                    </p>
                </div>
            @endif
        </div>
    </section>

    <!-- Asisten Pemeriksa Laporan Praktikum -->
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Asisten Pemeriksa Laporan Praktikum
            </h2>
            <p class="text-slate-500 mt-1">
                Data asisten pemeriksa laporan disesuaikan berdasarkan kelompok dan modul.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            @if (isset($asistenPemeriksa) && count($asistenPemeriksa) > 0)
                <table class="w-full min-w-[1100px] text-sm border border-slate-200">
                    <thead>
                        <tr class="bg-orange-500 text-white">
                            <th rowspan="2" class="border border-orange-600 p-3 text-center font-extrabold">
                                Kelompok
                            </th>

                            <th colspan="{{ $moduls->count() }}"
                                class="border border-orange-600 p-3 text-center font-extrabold">
                                Asisten Pemeriksa
                            </th>
                        </tr>

                        <tr class="bg-orange-400 text-slate-900">
                            @foreach ($moduls as $modul)
                                <th class="border border-orange-600 p-3 text-center font-extrabold whitespace-nowrap">
                                    {{ $modul->nama_modul }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($asistenPemeriksa as $item)
                            <tr>
                                <td class="border border-slate-300 bg-orange-200 p-3 text-center font-bold whitespace-nowrap">
                                    {{ $item->kelompok ?? '-' }}
                                </td>

                                @foreach ($moduls as $modul)
                                    <td class="border border-slate-300 p-3 text-center whitespace-nowrap">
                                        {{ $item->{'modul_' . $modul->urutan} ?? '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center">
                    <div class="text-4xl mb-3">👨‍🏫</div>

                    <h3 class="text-xl font-extrabold text-slate-900">
                        Data asisten pemeriksa belum tersedia
                    </h3>

                    <p class="text-slate-500 mt-2">
                        Data asisten pemeriksa laporan akan ditampilkan setelah diinput oleh koordinator.
                    </p>
                </div>
            @endif
        </div>
    </section>

    <!-- Jadwal Pengambilan dan Pengumpulan Laporan -->
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Jadwal Pengambilan dan Pengumpulan Laporan Praktikum
            </h2>
        </div>

        <div class="p-6 overflow-x-auto">
            @if (!empty($jadwalLaporan) && count($jadwalLaporan) > 0)
                <table class="w-full min-w-[900px] text-sm border border-slate-200">
                    <thead>
                        <tr class="bg-orange-300 text-slate-900">
                            <th class="border border-slate-300 p-3 text-center font-extrabold">Jam</th>
                            <th class="border border-slate-300 p-3 text-center font-extrabold">Senin</th>
                            <th class="border border-slate-300 p-3 text-center font-extrabold">Selasa</th>
                            <th class="border border-slate-300 p-3 text-center font-extrabold">Rabu</th>
                            <th class="border border-slate-300 p-3 text-center font-extrabold">Kamis</th>
                            <th class="border border-slate-300 p-3 text-center font-extrabold">Jumat</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($jadwalLaporan as $jadwal)
                            <tr>
                                <td class="border border-slate-300 bg-orange-200 p-4 text-center font-bold whitespace-nowrap">
                                    {{ $jadwal->jam ?? '-' }}
                                </td>

                                <td class="border border-slate-300 p-4 text-center font-bold {{ !empty($jadwal->senin) ? 'bg-green-200' : '' }}">
                                    {{ $jadwal->senin ?? '' }}
                                </td>

                                <td class="border border-slate-300 p-4 text-center font-bold {{ !empty($jadwal->selasa) ? 'bg-green-200' : '' }}">
                                    {{ $jadwal->selasa ?? '' }}
                                </td>

                                <td class="border border-slate-300 p-4 text-center font-bold {{ !empty($jadwal->rabu) ? 'bg-green-200' : '' }}">
                                    {{ $jadwal->rabu ?? '' }}
                                </td>

                                <td class="border border-slate-300 p-4 text-center font-bold {{ !empty($jadwal->kamis) ? 'bg-green-200' : '' }}">
                                    {{ $jadwal->kamis ?? '' }}
                                </td>

                                <td class="border border-slate-300 p-4 text-center font-bold {{ !empty($jadwal->jumat) ? 'bg-yellow-300' : '' }}">
                                    {{ $jadwal->jumat ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center">
                    <div class="text-4xl mb-3">🗓️</div>

                    <h3 class="text-xl font-extrabold text-slate-900">
                        Jadwal laporan belum tersedia
                    </h3>

                    <p class="text-slate-500 mt-2">
                        Data jadwal pengambilan dan pengumpulan laporan akan ditampilkan setelah diinput oleh koordinator.
                    </p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection