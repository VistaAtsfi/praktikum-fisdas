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
            <p class="text-sm text-slate-500">Asisten Pemeriksa</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                Per Modul
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
                <table class="w-full min-w-[1100px] text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="p-3 text-center rounded-l-xl">
                                Waktu
                            </th>

                            @foreach ($moduls as $modul)
                                <th class="p-3 text-center whitespace-nowrap {{ $loop->last ? 'rounded-r-xl' : '' }}">
                                    Modul {{ $loop->iteration }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($jadwalModulSesi as $minggu => $jadwals)
                            <tr>
                                <td colspan="{{ $moduls->count() + 1 }}"
                                    class="p-3 text-center font-extrabold text-blue-700 bg-blue-50">
                                    {{ $minggu }}
                                </td>
                            </tr>

                            @foreach ($jadwals as $jadwal)
                                <tr class="hover:bg-blue-50/40 transition">
                                    <td class="p-3 text-center font-bold text-slate-900 whitespace-nowrap">
                                        {{ $jadwal->waktu ?? '-' }}
                                    </td>

                                    @foreach ($moduls as $modul)
                                        @php
                                            $nomorModul = $loop->iteration;
                                        @endphp

                                        <td class="p-3 text-center whitespace-nowrap">
                                            {{ data_get($jadwal, 'modul_' . $nomorModul, '-') }}
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
                <table class="w-full min-w-[1100px] text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th rowspan="2" class="p-3 text-center rounded-l-xl">
                                Kelompok
                            </th>

                            <th colspan="{{ max($moduls->count(), 1) }}" class="p-3 text-center rounded-r-xl">
                                Asisten Pemeriksa
                            </th>
                        </tr>

                        <tr class="bg-slate-50 text-slate-700 border-t border-slate-200">
                            @forelse ($moduls as $modul)
                                <th class="p-3 text-center whitespace-nowrap">
                                    Modul {{ $loop->iteration }}
                                </th>
                            @empty
                                <th class="p-3 text-center">
                                    Modul
                                </th>
                            @endforelse
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($asistenPemeriksa as $item)
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="p-3 text-center font-bold text-slate-900 whitespace-nowrap">
                                    {{ $item->kelompok ?? '-' }}
                                </td>

                                @foreach ($moduls as $modul)
                                    @php
                                        $nomorModul = $loop->iteration;
                                    @endphp

                                    <td class="p-3 text-center whitespace-nowrap">
                                        {{ data_get($item, 'modul_' . $nomorModul, '-') }}
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
                <table class="w-full min-w-[900px] text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="p-3 text-center rounded-l-xl">Jam</th>
                            <th class="p-3 text-center">Senin</th>
                            <th class="p-3 text-center">Selasa</th>
                            <th class="p-3 text-center">Rabu</th>
                            <th class="p-3 text-center">Kamis</th>
                            <th class="p-3 text-center rounded-r-xl">Jumat</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($jadwalLaporan as $jadwal)
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="p-4 text-center font-bold text-slate-900 whitespace-nowrap">
                                    {{ $jadwal->jam ?? '-' }}
                                </td>

                                <td class="p-4 text-center font-semibold {{ !empty($jadwal->senin) ? 'bg-blue-50 text-blue-700' : '' }}">
                                    {{ $jadwal->senin ?? '' }}
                                </td>

                                <td class="p-4 text-center font-semibold {{ !empty($jadwal->selasa) ? 'bg-blue-50 text-blue-700' : '' }}">
                                    {{ $jadwal->selasa ?? '' }}
                                </td>

                                <td class="p-4 text-center font-semibold {{ !empty($jadwal->rabu) ? 'bg-blue-50 text-blue-700' : '' }}">
                                    {{ $jadwal->rabu ?? '' }}
                                </td>

                                <td class="p-4 text-center font-semibold {{ !empty($jadwal->kamis) ? 'bg-blue-50 text-blue-700' : '' }}">
                                    {{ $jadwal->kamis ?? '' }}
                                </td>

                                <td class="p-4 text-center font-semibold {{ !empty($jadwal->jumat) ? 'bg-blue-50 text-blue-700' : '' }}">
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