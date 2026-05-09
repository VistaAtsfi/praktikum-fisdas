@extends('layouts.praktikan', ['title' => 'Jadwal Praktikan'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Jadwal Praktikan
        </h1>

        <p class="text-slate-500 mt-2">
            Halaman ini menampilkan jadwal yang berkaitan dengan praktikan berdasarkan program studi,
            kelompok, dan sesi praktikum.
        </p>
    </section>

    <!-- Jadwal pribadi -->
    <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Program Studi</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->user->prodi ?? '-' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Kelompok</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->kelompok->nama_kelompok ?? 'Belum ditentukan' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Sesi</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $praktikan->kelompok->sesi ?? 'Belum ditentukan' }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 text-center">
            <p class="text-sm text-slate-500">Asisten</p>
            <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                {{ $asisten->user->name ?? 'Belum ditentukan' }}
            </h3>
        </div>
    </section>

    <!-- Pembagian Modul -->
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Pembagian Modul dan Sesi Praktikum
            </h2>
            <p class="text-slate-500 mt-1">
                Data ini nanti diinput oleh koordinator berdasarkan program studi.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-blue-50 text-blue-900">
                        <th class="p-3 text-left rounded-l-xl">Pertemuan</th>
                        <th class="p-3 text-left">Modul</th>
                        <th class="p-3 text-left">Kelompok</th>
                        <th class="p-3 text-left rounded-r-xl">Sesi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($moduls as $modul)
                        <tr>
                            <td class="p-3">Pertemuan {{ $modul->urutan }}</td>
                            <td class="p-3 font-semibold">{{ $modul->nama_modul }}</td>
                            <td class="p-3">{{ $praktikan->kelompok->nama_kelompok ?? '-' }}</td>
                            <td class="p-3">{{ $praktikan->kelompok->sesi ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-5 text-center text-slate-500">
                                Data modul belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Asisten Pemeriksa -->
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-4">
            Asisten Pemeriksa Laporan Praktikum
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">
                <p class="text-sm text-blue-700 font-bold">Nama Asisten</p>
                <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                    {{ $asisten->user->name ?? 'Belum ditentukan' }}
                </h3>
            </div>

            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">
                <p class="text-sm text-blue-700 font-bold">Email</p>
                <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                    {{ $asisten->user->email ?? '-' }}
                </h3>
            </div>

            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">
                <p class="text-sm text-blue-700 font-bold">Nomor Ponsel</p>
                <h3 class="text-xl font-extrabold text-slate-900 mt-2">
                    {{ $asisten->user->nomor_ponsel ?? '-' }}
                </h3>
            </div>
        </div>
    </section>

    <!-- Jadwal Laporan -->
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900">
            Jadwal Pengambilan dan Pengumpulan Laporan Praktikum
        </h2>

        <div class="mt-5 rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center">
            <div class="text-4xl mb-3">🗓️</div>
            <h3 class="text-xl font-extrabold text-slate-900">
                Jadwal laporan belum tersedia
            </h3>
            <p class="text-slate-500 mt-2">
                Data jadwal pengambilan dan pengumpulan laporan akan ditampilkan setelah diinput oleh koordinator.
            </p>
        </div>
    </section>
</div>
@endsection