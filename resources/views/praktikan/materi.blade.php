@extends('layouts.praktikan', ['title' => 'Materi Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Materi Praktikum</h1>
                <p class="text-slate-500 mt-2">
                    Materi disediakan oleh koordinator dan dapat dilihat atau diunduh oleh praktikan.
                </p>
            </div>

            <div class="px-4 py-3 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 font-bold">
                {{ $praktikan->praktikum->nama ?? '-' }}
            </div>
        </div>
    </section>

    <!-- Searching dan Sorting -->
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('praktikan.materi') }}" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Cari Materi
                </label>

                <input type="text"
                    name="q"
                    value="{{ $keyword ?? '' }}"
                    placeholder="Cari berdasarkan judul, deskripsi, atau modul..."
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Urutkan Berdasarkan
                </label>

                <select name="sort_by"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="modul_urutan" {{ ($sortBy ?? '') === 'modul_urutan' ? 'selected' : '' }}>
                        Urutan Modul
                    </option>
                    <option value="judul" {{ ($sortBy ?? '') === 'judul' ? 'selected' : '' }}>
                        Judul Materi
                    </option>
                    <option value="modul_nama" {{ ($sortBy ?? '') === 'modul_nama' ? 'selected' : '' }}>
                        Nama Modul
                    </option>
                    <option value="created_at" {{ ($sortBy ?? '') === 'created_at' ? 'selected' : '' }}>
                        Tanggal Input
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Arah Sorting
                </label>

                <select name="sort_direction"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="asc" {{ ($sortDirection ?? '') === 'asc' ? 'selected' : '' }}>
                        Naik
                    </option>
                    <option value="desc" {{ ($sortDirection ?? '') === 'desc' ? 'selected' : '' }}>
                        Turun
                    </option>
                </select>
            </div>

            <div class="lg:col-span-4 flex flex-col sm:flex-row gap-3">
                <button type="submit"
                    class="px-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan Sequential Search & Merge Sort
                </button>

                <a href="{{ route('praktikan.materi') }}"
                    class="px-6 py-3 rounded-2xl border border-slate-300 text-slate-700 text-center font-bold hover:bg-slate-50 transition">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <!-- Info Pengujian Algoritma -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl">
                    🔎
                </div>

                <div>
                    <h3 class="font-extrabold text-slate-900">Sequential Search</h3>
                    <p class="text-sm text-slate-500">Berjalan di lapisan aplikasi.</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 text-center mt-4">
                <div class="rounded-2xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Keyword</p>
                    <p class="font-bold text-slate-900 truncate">{{ $keyword ?: '-' }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Iterasi</p>
                    <p class="font-bold text-slate-900">{{ $searchResult['iterations'] ?? 0 }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Perbandingan</p>
                    <p class="font-bold text-slate-900">{{ $searchResult['comparisons'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-700 flex items-center justify-center text-2xl">
                    🔃
                </div>

                <div>
                    <h3 class="font-extrabold text-slate-900">Merge Sort</h3>
                    <p class="text-sm text-slate-500">Sorting data materi di lapisan aplikasi.</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 text-center mt-4">
                <div class="rounded-2xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Field</p>
                    <p class="font-bold text-slate-900 truncate">{{ $sortBy ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Arah</p>
                    <p class="font-bold text-slate-900">{{ $sortDirection ?? '-' }}</p>
                </div>

                <div class="rounded-2xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">Perbandingan</p>
                    <p class="font-bold text-slate-900">{{ $sortResult['comparisons'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Data Materi -->
    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($materis as $materi)
            <div class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition">
                <div class="p-6 bg-gradient-to-br from-blue-50 to-cyan-50 text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-white text-blue-700 flex items-center justify-center text-3xl shadow-sm mb-5">
                        📘
                    </div>

                    <p class="text-sm font-bold text-blue-700 mb-2">
                        {{ $materi->modul->nama_modul ?? 'Materi Umum' }}
                    </p>

                    <h3 class="text-xl font-extrabold text-slate-900 min-h-[56px]">
                        {{ $materi->judul }}
                    </h3>

                    <p class="text-slate-600 mt-3 min-h-[72px]">
                        {{ $materi->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>

                <div class="p-5 grid grid-cols-2 gap-3">
                    @if ($materi->link)
                        <a href="{{ $materi->link }}"
                           target="_blank"
                           class="py-3 rounded-2xl bg-slate-700 text-white text-center font-bold hover:bg-slate-800 transition">
                            Lihat
                        </a>

                        <a href="{{ $materi->link }}"
                           target="_blank"
                           class="py-3 rounded-2xl bg-blue-700 text-white text-center font-bold hover:bg-blue-800 transition">
                            Download
                        </a>
                    @else
                        <div class="col-span-2 py-3 rounded-2xl bg-slate-100 text-slate-500 text-center font-bold">
                            Link belum tersedia
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 bg-white rounded-3xl border border-blue-100 shadow-sm p-10 text-center">
                <div class="text-5xl mb-4">📚</div>
                <h3 class="text-xl font-extrabold text-slate-900">Materi tidak ditemukan</h3>
                <p class="text-slate-500 mt-2">
                    Tidak ada materi yang sesuai dengan pencarian.
                </p>
            </div>
        @endforelse
    </section>
</div>
@endsection