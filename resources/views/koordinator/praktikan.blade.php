@extends('layouts.koordinator', ['title' => 'Data Praktikan'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Data Praktikan</h1>
        <p class="text-slate-500 mt-2">Lihat data praktikan yang terdaftar pada periode praktikum.</p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('koordinator.praktikan') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIM, nama, atau email"
                   class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

            <select name="praktikum_id" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                @foreach ($praktikums as $praktikum)
                    <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                        {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }}
                    </option>
                @endforeach
            </select>

            <select name="prodi" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Prodi</option>
                @foreach ($prodis as $prodi)
                    <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>{{ $prodi }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                Terapkan
            </button>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">NIM</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">No. Ponsel</th>
                        <th class="p-3 text-center">Email</th>
                        <th class="p-3 text-center">Prodi</th>
                        <th class="p-3 text-center">Kelompok</th>
                        <th class="p-3 text-center rounded-r-xl">Sesi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($praktikans as $praktikan)
                        <tr>
                            <td class="p-3 text-center">{{ $praktikan->user->nim ?? '-' }}</td>
                            <td class="p-3 text-center font-semibold">{{ $praktikan->user->name ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->user->nomor_ponsel ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->user->email ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->user->prodi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->kelompok->nama_kelompok ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->kelompok->sesi ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-5 text-center text-slate-500">Data praktikan belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $praktikans->links() }}
            </div>
        </div>
    </section>

</div>
@endsection