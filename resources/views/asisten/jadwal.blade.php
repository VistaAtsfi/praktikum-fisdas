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
        <form method="GET" action="{{ route('asisten.jadwal') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Cari
                </label>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Kelompok / Modul"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>
                <select
                    name="prodi"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Prodi</option>
                    @foreach(($prodis ?? collect()) as $pItem)
                        <option
                            value="{{ $pItem }}"
                            @selected(request('prodi') == $pItem)>
                            {{ $pItem }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Kelompok
                </label>
                <select
                    name="kelompok_id"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Kelompok</option>
                    @foreach($kelompoks->groupBy('prodi') as $prodiName => $kelompokList)
                        <optgroup label="{{ $prodiName ?: 'Umum' }}">
                            @foreach($kelompokList as $kelompok)
                                <option
                                    value="{{ $kelompok->id }}"
                                    @selected(request('kelompok_id') == $kelompok->id)>
                                    {{ $kelompok->nama_kelompok }} ({{ $kelompok->prodi }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Modul
                </label>
                <select
                    name="modul_id"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Modul</option>
                    @foreach($moduls as $modul)
                        <option
                            value="{{ $modul->id }}"
                            @selected(request('modul_id') == $modul->id)>
                            {{ $modul->nama_modul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Hari
                </label>
                <select
                    name="hari"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
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
                            @selected(request('hari') == $hari)>
                            {{ $hari }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 lg:col-span-5 flex items-center gap-3">
                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition text-sm shadow-xs">
                    Filter
                </button>

                <a
                    href="{{ route('asisten.jadwal') }}"
                    class="px-6 py-2.5 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-sm">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Daftar Jadwal Praktikum
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Jadwal sesi dan alokasi modul praktikum per program studi.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">

            <table class="w-full text-sm">

                <thead>

                    <tr class="bg-slate-100/90 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200/60">

                        <th class="py-3.5 px-3 text-center font-bold rounded-l-xl w-24">Minggu</th>
                        <th class="py-3.5 px-3 text-left font-bold w-44">Hari & Jam</th>
                        <th class="py-3.5 px-4 text-left font-bold w-48">Kelompok & Prodi</th>
                        <th class="py-3.5 px-4 text-left font-bold">Modul Praktikum</th>
                        <th class="py-3.5 px-3 text-center font-bold rounded-r-xl w-28">Status</th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse($jadwals as $jadwal)

                        <tr class="hover:bg-blue-50/40 transition">

                            <td class="py-3.5 px-3 text-center align-middle">

                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold text-xs border border-blue-100">
                                    Minggu {{ $jadwal->minggu_ke }}
                                </span>

                            </td>

                            <td class="py-3.5 px-3 align-middle">

                                <div class="font-semibold text-slate-800">
                                    {{ $jadwal->hari }}
                                </div>

                                <div class="text-xs text-slate-500 font-medium flex items-center gap-1 mt-0.5 whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ substr($jadwal->jam_mulai,0,5) }} - {{ substr($jadwal->jam_selesai,0,5) }}</span>
                                </div>

                            </td>

                            <td class="py-3.5 px-4 align-middle">

                                <div class="font-bold text-slate-900">
                                    {{ $jadwal->kelompok->nama_kelompok ?? ($jadwal->kelompok_label ?: '-') }}
                                </div>

                                <div class="text-xs text-blue-600 font-medium mt-0.5">
                                    {{ $jadwal->prodi }}
                                </div>

                            </td>

                            <td class="py-3.5 px-4 align-middle">

                                <div class="font-semibold text-slate-800">
                                    {{ $jadwal->modul->nama_modul ?? '-' }}
                                </div>

                                @if($jadwal->keterangan)
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        {{ $jadwal->keterangan }}
                                    </div>
                                @endif

                            </td>

                            <td class="py-3.5 px-3 text-center align-middle">

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold whitespace-nowrap">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Terjadwal
                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="py-12 px-4 text-center text-slate-500">

                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="font-medium text-slate-600">Belum ada jadwal praktikum yang sesuai.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian Anda.</p>
                                </div>

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