@extends('layouts.asisten', ['title' => 'Materi Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Materi Praktikum
        </h1>

        <p class="text-slate-500 mt-2">
            Lihat dan unduh materi praktikum yang tersedia berdasarkan modul.
        </p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('asisten.materi') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Cari Materi
                </label>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari judul atau deskripsi materi"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Urutkan
                    </label>

                    <select name="sort"
                            class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        <option value="modul_asc" @selected(request('sort') === null || request('sort') === 'modul_asc')>
                            Modul 1 ke Modul Terakhir
                        </option>

                        <option value="modul_desc" @selected(request('sort') === 'modul_desc')>
                            Modul Terakhir ke Modul 1
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Saring Berdasarkan Modul
                    </label>

                    <select name="modul_id"
                            class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Modul</option>

                        @foreach ($moduls as $modul)
                            <option value="{{ $modul->id }}" @selected(request('modul_id') == $modul->id)>
                                Modul {{ $loop->iteration }} - {{ $modul->nama_modul }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan
                </button>

                <a href="{{ route('asisten.materi') }}"
                   class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($materis as $materi)
            @php
                $nomorModul = $moduls->pluck('id')->search($materi->modul_id);
            @endphp

            <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
                <div class="h-44 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-6xl mb-5">
                    📚
                </div>

                <p class="text-sm font-bold text-blue-700 mb-2">
                    @if ($nomorModul !== false)
                        Modul {{ $nomorModul + 1 }} -
                    @endif

                    {{ $materi->modul->nama_modul ?? 'Materi Umum' }}
                </p>

                <h2 class="text-2xl font-extrabold text-slate-900">
                    {{ $materi->judul }}
                </h2>

                <p class="text-slate-500 mt-3 leading-relaxed">
                    {{ $materi->deskripsi ?? 'Tidak ada deskripsi materi.' }}
                </p>

                @if ($materi->link)
                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ $materi->link }}"
                           target="_blank"
                           class="inline-flex justify-center px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                            Lihat Materi
                        </a>

                        <a href="{{ route('asisten.materi.download', $materi) }}"
                           class="inline-flex justify-center px-5 py-3 rounded-2xl bg-green-600 text-white font-extrabold hover:bg-green-700 transition">
                            Download Materi
                        </a>
                    </div>
                @else
                    <div class="mt-6 px-5 py-3 rounded-2xl bg-slate-100 text-slate-500 font-bold text-center">
                        Link materi belum tersedia
                    </div>
                @endif
            </div>
        @empty
            <div class="md:col-span-2 bg-white rounded-3xl border border-blue-100 shadow-sm p-8 text-center">
                <div class="text-5xl mb-4">📚</div>

                <h2 class="text-2xl font-extrabold text-slate-900">
                    Materi belum tersedia
                </h2>

                <p class="text-slate-500 mt-2">
                    Data materi akan ditampilkan setelah diinput oleh koordinator.
                </p>
            </div>
        @endforelse
    </section>

    @if (method_exists($materis, 'links'))
        <div>
            {{ $materis->links() }}
        </div>
    @endif

</div>
@endsection