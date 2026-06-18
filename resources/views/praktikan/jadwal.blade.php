@extends('layouts.praktikan', ['title' => 'Jadwal Praktikan'])

@section('content')
<div class="space-y-8 max-w-full overflow-x-hidden">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Jadwal Praktikan
        </h1>

        <p class="text-slate-500 mt-2 max-w-3xl">
            Halaman ini menampilkan jadwal yang berkaitan dengan praktikan berdasarkan program studi,
            kelompok, dan sesi praktikum.
        </p>
    </section>

    {{-- Jadwal Pribadi --}}
    <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">
                Program Studi
            </p>

            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->user->prodi ?? '-' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">
                Kelompok
            </p>

            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->kelompok->nama_kelompok ?? 'Belum ditentukan' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">
                Sesi
            </p>

            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->kelompok->sesi ?? 'Belum ditentukan' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">
                Asisten Pemeriksa
            </p>

            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                Per Modul
            </h3>
        </div>
    </section>

    {{-- Jadwal Pengambilan dan Pengumpulan Laporan --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Jadwal Pengambilan dan Pengumpulan Laporan Praktikum
            </h2>

            <p class="text-slate-500 mt-1">
                Jadwal laporan ditampilkan berdasarkan program studi Anda.
            </p>
        </div>

        <div class="p-4 sm:p-6 max-w-full overflow-x-hidden">
            @if (isset($jadwalLaporans) && $jadwalLaporans->count() > 0)
                <table class="w-full table-fixed text-[10px] sm:text-xs">
                    <thead class="bg-slate-100 text-slate-700">
                        <tr>
                            <th class="px-3 py-3 text-left w-[25%]">
                                Hari
                            </th>

                            <th class="px-3 py-3 text-left w-[25%]">
                                Jam
                            </th>

                            <th class="px-3 py-3 text-left w-[50%]">
                                Keterangan
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-blue-50">
                        @foreach ($jadwalLaporans as $jadwal)
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="px-3 py-4 font-bold text-slate-900">
                                    {{ $jadwal->hari ?? '-' }}
                                </td>

                                <td class="px-3 py-4 text-slate-700 font-semibold">
                                    {{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '-' }}
                                    -
                                    {{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '-' }}
                                    WITA
                                </td>

                                <td class="px-3 py-4 text-slate-600">
                                    {{ $jadwal->keterangan ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center">
                    <div class="text-4xl mb-3">
                        🗓️
                    </div>

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

    {{-- Pembagian Modul dan Sesi Praktikum --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Pembagian Modul dan Sesi Praktikum
            </h2>

            <p class="text-slate-500 mt-1">
                Jadwal berikut menampilkan hari dan waktu praktikum untuk kelompok Anda.
            </p>
        </div>

        <div class="p-4 sm:p-6 max-w-full overflow-x-hidden">
            @if (isset($jadwalModulPraktikums) && $jadwalModulPraktikums->count() > 0)
                <div class="space-y-6">
                    @foreach ($jadwalModulPraktikums as $mingguKe => $rows)
                        <div class="rounded-3xl border border-blue-100 overflow-hidden">
                            <div class="px-5 py-4 bg-blue-50 border-b border-blue-100">
                                <h3 class="text-lg font-black text-slate-900">
                                    Minggu {{ $mingguKe }}
                                </h3>
                            </div>

                            <table class="w-full table-fixed text-[10px] sm:text-xs">
                                <thead class="bg-slate-100 text-slate-700">
                                    <tr>
                                        <th class="px-3 py-3 text-left w-[16%]">
                                            Hari
                                        </th>

                                        <th class="px-3 py-3 text-left w-[20%]">
                                            Jam
                                        </th>

                                        <th class="px-3 py-3 text-left w-[34%]">
                                            Modul
                                        </th>

                                        <th class="px-3 py-3 text-left w-[15%]">
                                            Kelompok
                                        </th>

                                        <th class="px-3 py-3 text-left w-[15%]">
                                            Keterangan
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-blue-50">
                                    @foreach ($rows as $jadwal)
                                        <tr class="hover:bg-blue-50/40 transition">
                                            <td class="px-3 py-4 font-bold text-slate-900">
                                                {{ $jadwal->hari ?? '-' }}
                                            </td>

                                            <td class="px-3 py-4 font-bold text-slate-900">
                                                {{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '-' }}
                                                -
                                                {{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '-' }}
                                                WITA
                                            </td>

                                            <td class="px-3 py-4 text-slate-700">
                                                @if ($jadwal->modul)
                                                    <span class="font-bold">
                                                        Modul {{ $jadwal->modul->nomor_modul }}
                                                    </span>

                                                    <span class="block text-slate-500 mt-1">
                                                        {{ $jadwal->modul->nama_modul }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td class="px-3 py-4 text-slate-700 font-semibold">
                                                {{ $jadwal->kelompok->nama_kelompok ?? $jadwal->kelompok_label ?? '-' }}
                                            </td>

                                            <td class="px-3 py-4 text-slate-600">
                                                {{ $jadwal->keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center">
                    <div class="text-4xl mb-3">
                        📋
                    </div>

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

    {{-- Asisten Pemeriksa Laporan Praktikum --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Asisten Pemeriksa Laporan Praktikum
            </h2>

            <p class="text-slate-500 mt-1">
                Data asisten pemeriksa laporan disesuaikan berdasarkan kelompok dan modul.
            </p>
        </div>

        <div class="p-4 sm:p-6 max-w-full overflow-x-hidden">
            @if (isset($asistenPemeriksa) && $asistenPemeriksa->count() > 0)
                <table class="w-full table-fixed text-[10px] sm:text-xs">
                    <thead class="bg-slate-100 text-slate-700">
                        <tr>
                            <th class="px-3 py-3 text-left w-[20%]">
                                Modul
                            </th>

                            <th class="px-3 py-3 text-left w-[30%]">
                                Asisten
                            </th>

                            <th class="px-3 py-3 text-left w-[25%]">
                                Kelompok
                            </th>

                            <th class="px-3 py-3 text-left w-[25%]">
                                Program Studi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-blue-50">
                        @foreach ($asistenPemeriksa as $item)
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="px-3 py-4 font-bold text-slate-900">
                                    @if ($item->modul)
                                        Modul {{ $item->modul->nomor_modul }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-3 py-4 text-slate-700 font-semibold">
                                    {{ $item->asisten->user->name ?? '-' }}
                                </td>

                                <td class="px-3 py-4 text-slate-600">
                                    {{ $item->kelompok->nama_kelompok ?? '-' }}
                                </td>

                                <td class="px-3 py-4 text-slate-600">
                                    {{ $item->prodi ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center">
                    <div class="text-4xl mb-3">
                        👨‍🏫
                    </div>

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
</div>
@endsection