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

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">
            Tambah Periode Praktikum
        </h2>

        <form method="POST" action="{{ route('koordinator.periode.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Praktikum</label>
                <input type="text" name="nama" required
                       placeholder="Praktikum Fisika Dasar 1"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Praktikum</label>
                <input type="text" name="jenis_praktikum" required
                       placeholder="Fisika Dasar"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tahun Akademik</label>
                <input type="text" name="tahun_akademik" required
                       placeholder="2025/2026"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Semester</label>
                <select name="semester" required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Semester</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>

            <label class="md:col-span-2 inline-flex items-center gap-2">
                <input type="checkbox" name="is_aktif" value="1"
                       class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                <span class="text-sm font-semibold text-slate-700">Jadikan periode aktif</span>
            </label>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Tambah Periode
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <form method="GET" action="{{ route('koordinator.periode') }}">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari periode praktikum"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
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
                            <td class="p-3 text-center font-bold">{{ $praktikum->nama }}</td>
                            <td class="p-3 text-center">{{ $praktikum->jenis_praktikum }}</td>
                            <td class="p-3 text-center">{{ $praktikum->tahun_akademik }}</td>
                            <td class="p-3 text-center">{{ $praktikum->semester }}</td>
                            <td class="p-3 text-center">
                                @if ($praktikum->is_aktif)
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-bold text-xs">Aktif</span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 font-bold text-xs">Nonaktif</span>
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