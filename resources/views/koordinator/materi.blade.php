@extends('layouts.koordinator', ['title' => 'Materi Praktikum'])

@section('content')
<div class="space-y-8"
     x-data="{
        deleteModal: false,
        deleteUrl: '',
        deleteTitle: '',
        openDelete(url, title) {
            this.deleteUrl = url;
            this.deleteTitle = title;
            this.deleteModal = true;
        },
        closeDelete() {
            this.deleteModal = false;
            this.deleteUrl = '';
            this.deleteTitle = '';
        }
     }">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Materi Praktikum
        </h1>

        <p class="text-slate-500 mt-2">
            Kelola materi praktikum berdasarkan periode dan modul.
        </p>
    </section>

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3">
                Terjadi kesalahan input:
            </h2>

            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">
            Tambah Materi Praktikum
        </h2>

        <form method="POST"
              action="{{ route('koordinator.materi.store') }}"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
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

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nama Modul
                </label>

                <input type="text"
                       name="nama_modul"
                       value="{{ old('nama_modul') }}"
                       placeholder="Contoh: Modul 1 - Hukum Kekekalan Energi Mekanik"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Judul Materi
                </label>

                <input type="text"
                       name="judul"
                       value="{{ old('judul') }}"
                       required
                       placeholder="Masukkan judul materi"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Deskripsi
                </label>

                <textarea name="deskripsi"
                          rows="3"
                          placeholder="Masukkan deskripsi singkat materi"
                          class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Link Materi
                </label>

                <input type="text"
                       name="link"
                       value="{{ old('link') }}"
                       placeholder="Masukkan link Google Drive atau link materi"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Materi
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET"
              action="{{ route('koordinator.materi') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Cari Materi
                </label>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari judul, deskripsi, atau nama modul"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Periode Praktikum
                </label>

                <select name="praktikum_id"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Cari
                </button>

                <a href="{{ route('koordinator.materi') }}"
                   class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($materis as $materi)
            <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
                @php
                    $nomorModul = $moduls->pluck('id')->search($materi->modul_id);
                @endphp

                <p class="text-xs font-extrabold text-blue-700 uppercase">
                    @if ($materi->modul && $nomorModul !== false)
                        Modul {{ $nomorModul + 1 }} - {{ $materi->modul->nama_modul }}
                    @elseif ($materi->modul)
                        {{ $materi->modul->nama_modul }}
                    @else
                        Materi Umum
                    @endif
                </p>

                <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                    {{ $materi->judul }}
                </h3>

                <p class="text-slate-500 mt-3 leading-relaxed">
                    {{ $materi->deskripsi ?? 'Tidak ada deskripsi.' }}
                </p>

                <div class="mt-5 flex flex-wrap gap-3">
                    @if ($materi->link)
                        <a href="{{ $materi->link }}"
                           target="_blank"
                           class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                            Lihat Materi
                        </a>
                    @endif

                    <button type="button"
                            @click="openDelete('{{ route('koordinator.materi.delete', $materi) }}', '{{ addslashes($materi->judul) }}')"
                            class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition">
                        Hapus
                    </button>
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

    {{-- Modal Konfirmasi Hapus --}}
    <div x-cloak
         x-show="deleteModal"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">

        <div @click.outside="closeDelete()"
             x-transition
             class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-red-100 overflow-hidden">

            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-3xl mb-4">
                    ⚠️
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900">
                    Hapus Materi?
                </h2>

                <p class="text-slate-500 mt-3 leading-relaxed">
                    Materi
                    <span class="font-bold text-slate-800" x-text="deleteTitle"></span>
                    akan dihapus dari sistem.
                </p>

                <p class="text-sm text-red-500 mt-2">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="px-6 pb-6 grid grid-cols-2 gap-3">
                <button type="button"
                        @click="closeDelete()"
                        class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Batal
                </button>

                <form method="POST" x-bind:action="deleteUrl">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="w-full px-5 py-3 rounded-2xl bg-red-600 text-white font-extrabold hover:bg-red-700 transition">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection