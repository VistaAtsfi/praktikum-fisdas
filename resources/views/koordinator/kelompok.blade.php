@extends('layouts.koordinator', ['title' => 'Kelompok Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Kelompok Praktikum</h1>
        <p class="text-slate-500 mt-2">Kelola kelompok praktikum berdasarkan periode.</p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Tambah Kelompok</h2>

        <form method="POST" action="{{ route('koordinator.kelompok.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Periode Praktikum</label>
                <select name="praktikum_id" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Periode</option>
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                            {{ $praktikum->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kelompok</label>
                <input type="text" name="nama_kelompok" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Sesi</label>
                <input type="text" name="sesi" placeholder="Sesi 1" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Tambah Kelompok
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <form method="GET" action="{{ route('koordinator.kelompok') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kelompok"
                       class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <select name="praktikum_id" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                            {{ $praktikum->nama }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan
                </button>
            </form>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">No.</th>
                        <th class="p-3 text-center">Nama Kelompok</th>
                        <th class="p-3 text-center">Sesi</th>
                        <th class="p-3 text-center">Jumlah Praktikan</th>
                        <th class="p-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($kelompoks as $kelompok)
                        <tr>
                            <td class="p-3 text-center">{{ $loop->iteration + ($kelompoks->currentPage() - 1) * $kelompoks->perPage() }}</td>
                            <td class="p-3 text-center font-semibold">{{ $kelompok->nama_kelompok }}</td>
                            <td class="p-3 text-center">{{ $kelompok->sesi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $kelompok->praktikans->count() }}</td>
                            <td class="p-3 text-center">
                                <a href="{{ route('koordinator.kelompok.show', $kelompok) }}"
                                   class="inline-flex px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-5 text-center text-slate-500">Data kelompok belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $kelompoks->links() }}
            </div>
        </div>
    </section>

</div>
@endsection