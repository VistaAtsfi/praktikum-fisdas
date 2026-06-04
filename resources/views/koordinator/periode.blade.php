@extends('layouts.koordinator', ['title' => 'Periode Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Periode Praktikum
        </h1>

        <p class="text-slate-500 mt-2">
            Tambahkan dan kelola periode praktikum yang digunakan pada sistem.
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
            Tambah Periode Praktikum
        </h2>

        <form method="POST" action="{{ route('koordinator.periode.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nama Praktikum
                </label>

                <select name="nama"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Nama Praktikum</option>

                    <option value="Praktikum Fisika Dasar 1" @selected(old('nama') === 'Praktikum Fisika Dasar 1')>
                        Praktikum Fisika Dasar 1
                    </option>

                    <option value="Praktikum Fisika Dasar 2" @selected(old('nama') === 'Praktikum Fisika Dasar 2')>
                        Praktikum Fisika Dasar 2
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jenis Praktikum
                </label>

                <select name="jenis_praktikum"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Jenis Praktikum</option>

                    <option value="fisdas_1" @selected(old('jenis_praktikum') === 'fisdas_1')>
                        Fisdas 1
                    </option>

                    <option value="fisdas_2" @selected(old('jenis_praktikum') === 'fisdas_2')>
                        Fisdas 2
                    </option>
                </select>

                <p class="text-xs text-slate-400 mt-2">
                    Jenis praktikum dipakai sebagai kode internal sistem.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Tahun Akademik
                </label>

                <input type="text"
                       name="tahun_akademik"
                       value="{{ old('tahun_akademik') }}"
                       required
                       pattern="[0-9]{4}/[0-9]{4}"
                       placeholder="2025/2026"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-slate-400 mt-2">
                    Gunakan format tahun/tahun, contoh: 2025/2026.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Semester
                </label>

                <select name="semester"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Semester</option>
                    <option value="Ganjil" @selected(old('semester') === 'Ganjil')>Ganjil</option>
                    <option value="Genap" @selected(old('semester') === 'Genap')>Genap</option>
                </select>
            </div>

            <label class="md:col-span-2 inline-flex items-center gap-2">
                <input type="checkbox"
                       name="is_aktif"
                       value="1"
                       @checked(old('is_aktif'))
                       class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">

                <span class="text-sm font-semibold text-slate-700">
                    Jadikan periode aktif
                </span>
            </label>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Tambah Periode
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">
            Program Studi Peserta Praktikum
        </h2>

        <p class="text-slate-500 mb-5">
            Tambahkan program studi yang mengikuti periode praktikum. Data ini akan menjadi pilihan dropdown prodi pada menu berikutnya.
        </p>

        <form method="POST"
            action="{{ route('koordinator.periode.prodi.store') }}"
            class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        <option value="{{ $praktikum->id }}">
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }} {{ $praktikum->semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nama Program Studi
                </label>

                <input type="text"
                    name="nama_prodi"
                    required
                    placeholder="Contoh: Informatika"
                    class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Tambah Prodi
                </button>
            </div>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">Periode Praktikum</th>
                        <th class="p-3 text-center">Program Studi</th>
                        <th class="p-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse (($praktikumProdis ?? collect()) as $praktikumId => $prodis)
                        @foreach ($prodis as $prodi)
                            <tr>
                                <td class="p-3 text-center">
                                    {{ $prodi->praktikum->nama ?? '-' }} -
                                    {{ $prodi->praktikum->tahun_akademik ?? '-' }}
                                    {{ $prodi->praktikum->semester ?? '' }}
                                </td>

                                <td class="p-3 text-center font-bold">
                                    {{ $prodi->nama_prodi }}
                                </td>

                                <td class="p-3 text-center">
                                    <form method="POST"
                                        action="{{ route('koordinator.periode.prodi.delete', $prodi) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus program studi ini dari periode praktikum?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="3" class="p-5 text-center text-slate-500">
                                Program studi peserta praktikum belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <form method="GET"
                  action="{{ route('koordinator.periode') }}"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Cari Periode
                    </label>

                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama praktikum, jenis, semester"
                           class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        Tahun Akademik
                    </label>

                    <select name="tahun_akademik"
                            class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Tahun</option>

                        @foreach (($tahunAkademikOptions ?? collect()) as $tahun)
                            <option value="{{ $tahun }}" @selected(request('tahun_akademik') === $tahun)>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-3">
                    <button type="submit"
                            class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                        Cari
                    </button>

                    <a href="{{ route('koordinator.periode') }}"
                       class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">Nama</th>
                        <th class="p-3 text-center">Jenis</th>
                        <th class="p-3 text-center">Tahun Akademik</th>
                        <th class="p-3 text-center">Semester</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($praktikums as $praktikum)
                        <tr>
                            <td class="p-3 text-center font-bold">
                                {{ $praktikum->nama }}
                            </td>

                            <td class="p-3 text-center">
                                @if ($praktikum->jenis_praktikum === 'fisdas_1')
                                    Fisdas 1
                                @elseif ($praktikum->jenis_praktikum === 'fisdas_2')
                                    Fisdas 2
                                @else
                                    {{ $praktikum->jenis_praktikum ?? '-' }}
                                @endif
                            </td>

                            <td class="p-3 text-center">
                                {{ $praktikum->tahun_akademik }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $praktikum->semester }}
                            </td>

                            <td class="p-3 text-center">
                                @if ($praktikum->is_aktif)
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-bold text-xs">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 font-bold text-xs">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="p-3 text-center">
                                <a href="{{ route('koordinator.periode.show', $praktikum) }}"
                                   class="inline-flex px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-5 text-center text-slate-500">
                                Periode praktikum belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $praktikums->links() }}
            </div>
        </div>
    </section>

</div>
@endsection