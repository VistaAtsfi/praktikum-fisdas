@extends('layouts.koordinator', ['title' => 'Materi Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Materi Praktikum</h1>
        <p class="text-slate-500 mt-2">Kelola materi praktikum berdasarkan periode dan modul.</p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Tambah Materi Praktikum</h2>

        <form method="POST" action="{{ route('koordinator.materi.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Periode Praktikum</label>
                <select name="praktikum_id" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Periode</option>
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }} {{ $praktikum->semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Modul</label>
                <select name="modul_id" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Materi Umum</option>
                    @foreach ($moduls as $modul)
                        <option value="{{ $modul->id }}">{{ $modul->nama_modul }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Judul Materi</label>
                <input type="text" name="judul" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Link Materi</label>
                <input type="text" name="link" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Materi
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('koordinator.materi') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari materi"
                   class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

            <select name="praktikum_id" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                @foreach ($praktikums as $praktikum)
                    <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                        {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-3">
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan
                </button>

                <a href="{{ route('koordinator.materi') }}" class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($materis as $materi)
            <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
                <p class="text-xs font-extrabold text-blue-700 uppercase">
                    {{ $materi->modul->nama_modul ?? 'Materi Umum' }}
                </p>

                <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                    {{ $materi->judul }}
                </h3>

                <p class="text-slate-500 mt-3">
                    {{ $materi->deskripsi ?? 'Tidak ada deskripsi.' }}
                </p>

                <div class="mt-5 flex flex-wrap gap-3">
                    @if ($materi->link)
                        <a href="{{ $materi->link }}" target="_blank"
                           class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                            Lihat Materi
                        </a>
                    @endif

                    <form method="POST" action="{{ route('koordinator.materi.delete', $materi) }}"
                          onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 bg-white rounded-3xl border border-blue-100 p-8 text-center text-slate-500">
                Data materi belum tersedia.
            </div>
        @endforelse
    </section>

    <div>
        {{ $materis->links() }}
    </div>

</div>
@endsection