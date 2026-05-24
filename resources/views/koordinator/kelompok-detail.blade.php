@extends('layouts.koordinator', ['title' => 'Detail Kelompok'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">Detail Kelompok</h1>
                <p class="text-slate-500 mt-2">{{ $kelompok->praktikum->nama ?? '-' }}</p>
            </div>

            <a href="{{ route('koordinator.kelompok') }}"
               class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                Kembali
            </a>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="POST" action="{{ route('koordinator.kelompok.update', $kelompok) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kelompok</label>
                <input type="text" name="nama_kelompok" value="{{ $kelompok->nama_kelompok }}" required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Sesi</label>
                <input type="text" name="sesi" value="{{ $kelompok->sesi }}"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">Anggota Kelompok</h2>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[800px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">NIM</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">Prodi</th>
                        <th class="p-3 text-center rounded-r-xl">Jenis Kelamin</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($kelompok->praktikans as $praktikan)
                        <tr>
                            <td class="p-3 text-center">{{ $praktikan->user->nim ?? '-' }}</td>
                            <td class="p-3 text-center font-semibold">{{ $praktikan->user->name ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->user->prodi ?? '-' }}</td>
                            <td class="p-3 text-center">
                                {{ match ($praktikan->user->jenis_kelamin ?? null) {
                                    'L' => 'Laki-laki',
                                    'P' => 'Perempuan',
                                    default => '-',
                                } }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-5 text-center text-slate-500">
                                Belum ada praktikan pada kelompok ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection