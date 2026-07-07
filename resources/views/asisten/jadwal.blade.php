@extends('layouts.asisten', ['title' => 'Jadwal Praktikum'])

@section('content')

<div class="space-y-8">

    <section class="bg-gradient-to-br from-blue-900 via-blue-800 to-cyan-700 rounded-3xl p-8 text-white shadow-xl">

        <div class="grid md:grid-cols-2 gap-6 items-center">

            <div>

                <h1 class="text-3xl font-extrabold mb-3">
                    Jadwal Praktikum
                </h1>

                <p class="text-blue-100 leading-relaxed">
                    Halaman ini menampilkan seluruh jadwal pelaksanaan praktikum berdasarkan
                    modul, kelompok, minggu pelaksanaan, serta hari dan jam praktikum.
                </p>

            </div>

            <div class="grid grid-cols-2 gap-4">

                <div class="bg-white/10 rounded-2xl p-5 border border-white/20">

                    <p class="text-sm text-blue-100">
                        Total Jadwal
                    </p>

                    <h2 class="text-3xl font-extrabold mt-2">
                        {{ $jadwals->total() }}
                    </h2>

                </div>

                <div class="bg-white/10 rounded-2xl p-5 border border-white/20">

                    <p class="text-sm text-blue-100">
                        Kelompok
                    </p>

                    <h2 class="text-3xl font-extrabold mt-2">
                        {{ $kelompoks->count() }}
                    </h2>

                </div>

                <div class="bg-white/10 rounded-2xl p-5 border border-white/20">

                    <p class="text-sm text-blue-100">
                        Modul
                    </p>

                    <h2 class="text-3xl font-extrabold mt-2">
                        {{ $moduls->count() }}
                    </h2>

                </div>

                <div class="bg-white/10 rounded-2xl p-5 border border-white/20">

                    <p class="text-sm text-blue-100">
                        Praktikum
                    </p>

                    <h2 class="font-bold mt-2">
                        {{ $praktikum->nama ?? '-' }}
                    </h2>

                </div>

            </div>

        </div>

    </section>





    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">

        <form method="GET">

            <div class="grid lg:grid-cols-4 gap-4">

                <div>

                    <label class="font-semibold text-sm mb-2 block">
                        Cari
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Kelompok / Modul / Prodi"
                        class="w-full rounded-xl border-blue-200 focus:border-blue-500 focus:ring-blue-500">

                </div>

                <div>

                    <label class="font-semibold text-sm mb-2 block">
                        Kelompok
                    </label>

                    <select
                        name="kelompok_id"
                        class="w-full rounded-xl border-blue-200 focus:border-blue-500 focus:ring-blue-500">

                        <option value="">Semua Kelompok</option>

                        @foreach($kelompoks as $kelompok)

                            <option
                                value="{{ $kelompok->id }}"
                                @selected(request('kelompok_id')==$kelompok->id)>

                                {{ $kelompok->nama_kelompok }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="font-semibold text-sm mb-2 block">
                        Modul
                    </label>

                    <select
                        name="modul_id"
                        class="w-full rounded-xl border-blue-200 focus:border-blue-500 focus:ring-blue-500">

                        <option value="">Semua Modul</option>

                        @foreach($moduls as $modul)

                            <option
                                value="{{ $modul->id }}"
                                @selected(request('modul_id')==$modul->id)>

                                {{ $modul->nama_modul }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="font-semibold text-sm mb-2 block">
                        Hari
                    </label>

                    <select
                        name="hari"
                        class="w-full rounded-xl border-blue-200 focus:border-blue-500 focus:ring-blue-500">

                        <option value="">Semua Hari</option>

                        @foreach([
                            'Senin',
                            'Selasa',
                            'Rabu',
                            'Kamis',
                            'Jumat',
                            'Sabtu',
                            'Minggu'
                        ] as $hari)

                            <option
                                value="{{ $hari }}"
                                @selected(request('hari')==$hari)>

                                {{ $hari }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="mt-6 flex gap-3">

                <button
                    class="px-6 py-3 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">

                    Filter

                </button>

                <a
                    href="{{ route('asisten.jadwal') }}"
                    class="px-6 py-3 rounded-xl bg-slate-200 font-bold hover:bg-slate-300 transition">

                    Reset

                </a>

            </div>

        </form>

    </section>






    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">

        <div class="overflow-x-auto">

            <table class="min-w-[1200px] w-full text-sm">

                <thead>

                    <tr class="bg-slate-100">

                        <th class="p-3 rounded-l-xl">Minggu</th>
                        <th class="p-3">Hari</th>
                        <th class="p-3">Jam</th>
                        <th class="p-3">Kelompok</th>
                        <th class="p-3">Prodi</th>
                        <th class="p-3">Modul</th>
                        <th class="p-3">Keterangan</th>
                        <th class="p-3 rounded-r-xl">Status</th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($jadwals as $jadwal)

                        <tr class="hover:bg-slate-50">

                            <td class="p-4 text-center font-bold">

                                Minggu {{ $jadwal->minggu_ke }}

                            </td>

                            <td class="p-4 text-center">

                                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">

                                    {{ $jadwal->hari }}

                                </span>

                            </td>

                            <td class="p-4 text-center">

                                {{ substr($jadwal->jam_mulai,0,5) }}
                                -
                                {{ substr($jadwal->jam_selesai,0,5) }}

                            </td>

                            <td class="p-4 text-center">

                                <div class="font-bold">

                                    {{ $jadwal->kelompok->nama_kelompok ?? '-' }}

                                </div>

                                <div class="text-xs text-slate-500">

                                    {{ $jadwal->kelompok_label }}

                                </div>

                            </td>

                            <td class="p-4 text-center">

                                {{ $jadwal->prodi }}

                            </td>

                            <td class="p-4">

                                <div class="font-bold">

                                    {{ $jadwal->modul->nama_modul ?? '-' }}

                                </div>

                            </td>

                            <td class="p-4">

                                {{ $jadwal->keterangan ?: '-' }}

                            </td>

                            <td class="p-4 text-center">

                                <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 font-bold">

                                    Terjadwal

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="p-8 text-center text-slate-500">

                                Belum ada jadwal praktikum.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($jadwals->hasPages())

            <div class="mt-6">

                {{ $jadwals->links() }}

            </div>

        @endif

    </section>

</div>

@endsection