@extends('layouts.koordinator', ['title' => 'Nilai Akhir'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Nilai Akhir</h1>
        <p class="text-slate-500 mt-2">Lihat dan unduh rekap nilai akhir praktikan.</p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('koordinator.nilai-akhir') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari NIM atau nama"
                   class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

            <select name="praktikum_id" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                @foreach ($praktikums as $praktikum)
                    <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                        {{ $praktikum->nama }}
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

        <div class="mt-5">
            <a href="{{ route('koordinator.nilai-akhir.export', request()->query()) }}"
               class="inline-flex px-5 py-3 rounded-2xl bg-green-600 text-white font-extrabold hover:bg-green-700 transition">
                Unduh File Excel/CSV
            </a>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1300px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">NIM</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">Prodi</th>
                        <th class="p-3 text-center">Kelompok</th>
                        <th class="p-3 text-center">Kehadiran</th>
                        <th class="p-3 text-center">Laporan</th>
                        <th class="p-3 text-center">Responsi</th>
                        <th class="p-3 text-center">Asistensi</th>
                        <th class="p-3 text-center">Kemampuan Praktik</th>
                        <th class="p-3 text-center">Nilai Angka</th>
                        <th class="p-3 text-center">Bobot</th>
                        <th class="p-3 text-center rounded-r-xl">Huruf</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($nilaiAkhirs as $nilai)
                        <tr>
                            <td class="p-3 text-center">{{ $nilai->praktikan->user->nim ?? '-' }}</td>
                            <td class="p-3 text-center font-semibold">{{ $nilai->praktikan->user->name ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->praktikan->user->prodi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->praktikan->kelompok->nama_kelompok ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->kehadiran ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->laporan ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->responsi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->asistensi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->kemampuan_praktik ?? '-' }}</td>
                            <td class="p-3 text-center font-extrabold">{{ $nilai->nilai_angka ?? '-' }}</td>
                            <td class="p-3 text-center font-extrabold">{{ $nilai->nilai_bobot ?? '-' }}</td>
                            <td class="p-3 text-center font-extrabold">{{ $nilai->nilai_huruf ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="p-5 text-center text-slate-500">Data nilai akhir belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $nilaiAkhirs->links() }}
            </div>
        </div>
    </section>

</div>
@endsection