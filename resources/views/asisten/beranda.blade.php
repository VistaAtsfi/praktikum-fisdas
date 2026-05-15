@extends('layouts.asisten', ['title' => 'Beranda Asisten Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-gradient-to-br from-blue-900 via-blue-800 to-cyan-700 rounded-3xl p-8 text-white shadow-xl overflow-hidden relative">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>

        <div class="relative">
            <p class="text-blue-100 font-semibold mb-2">
                Selamat datang,
            </p>

            <h1 class="text-3xl md:text-4xl font-extrabold mb-4">
                {{ auth()->user()->name }}
            </h1>

            <p class="text-blue-100 leading-relaxed max-w-3xl">
                Di halaman beranda asisten ini, Anda dapat memantau data praktikan, presensi, materi, dan penilaian praktikum.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                    <p class="text-blue-100 text-sm">Praktikum</p>
                    <p class="font-extrabold">{{ $praktikum->nama ?? '-' }}</p>
                </div>

                <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                    <p class="text-blue-100 text-sm">Kelompok</p>
                    <p class="font-extrabold">{{ $kelompoks->count() }} Kelompok</p>
                </div>

                <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                    <p class="text-blue-100 text-sm">Praktikan</p>
                    <p class="font-extrabold">{{ $praktikans->count() }} Praktikan</p>
                </div>

                <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                    <p class="text-blue-100 text-sm">Status</p>
                    <p class="font-extrabold">{{ ucfirst($asisten->status ?? '-') }}</p>
                </div>
            </div>
        </div>
    </section>

    @if (!$asisten)
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="text-xl font-extrabold text-red-700">
                Data asisten belum tersedia
            </h2>
            <p class="text-red-600 mt-2">
                Akun ini memiliki role asisten, tetapi belum terhubung ke data asisten dan praktikum.
            </p>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 text-center mb-6">
            Profil
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <p><span class="font-bold text-slate-700">Email:</span> {{ auth()->user()->email }}</p>
                <p><span class="font-bold text-slate-700">Nama Lengkap:</span> {{ auth()->user()->name }}</p>
                <p><span class="font-bold text-slate-700">NIM:</span> {{ auth()->user()->nim ?? '-' }}</p>
                <p><span class="font-bold text-slate-700">Program Studi:</span> {{ auth()->user()->prodi ?? '-' }}</p>
            </div>

            <div class="space-y-3">
                <p><span class="font-bold text-slate-700">Jenis Kelamin:</span> {{ auth()->user()->jenis_kelamin ?? '-' }}</p>
                <p><span class="font-bold text-slate-700">Nomor Ponsel:</span> {{ auth()->user()->nomor_ponsel ?? '-' }}</p>
                <p><span class="font-bold text-slate-700">Praktikum:</span> {{ $praktikum->nama ?? '-' }}</p>
                <p><span class="font-bold text-slate-700">Status:</span> {{ ucfirst($asisten->status ?? '-') }}</p>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 text-center mb-6">
            Data Kelompok
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-center">
                <p class="text-sm text-blue-700 font-bold">Jumlah Kelompok</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $kelompoks->count() }}</h3>
            </div>

            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-center">
                <p class="text-sm text-blue-700 font-bold">Jumlah Praktikan</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $praktikans->count() }}</h3>
            </div>

            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-center">
                <p class="text-sm text-blue-700 font-bold">Jumlah Materi</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $jumlahMateri }}</h3>
            </div>

            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-center">
                <p class="text-sm text-blue-700 font-bold">Total Presensi</p>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-2">{{ $jumlahPresensi }}</h3>
            </div>
        </div>

        <p class="font-bold text-slate-800 mb-3">
            Praktikan yang belum melakukan asistensi:
        </p>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">NIM</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">No. Ponsel</th>
                        <th class="p-3 text-center">Email</th>
                        <th class="p-3 text-center">Prodi</th>
                        <th class="p-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($praktikanBelumAsistensi as $praktikan)
                        <tr>
                            <td class="p-3 text-center">{{ $praktikan->user->nim ?? '-' }}</td>
                            <td class="p-3 text-center font-semibold">{{ $praktikan->user->name ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->user->nomor_ponsel ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->user->email ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->user->prodi ?? '-' }}</td>
                            <td class="p-3 text-center">
                                <a href="{{ route('asisten.penilaian.show', $praktikan) }}"
                                   class="inline-flex px-3 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-5 text-center text-slate-500">
                                Tidak ada data praktikan yang perlu ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection