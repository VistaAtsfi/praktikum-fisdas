@extends('layouts.koordinator', ['title' => 'Nilai Akhir'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Nilai Akhir
        </h1>

        <p class="text-slate-500 mt-2">
            Lihat dan unduh rekap nilai akhir praktikan.
        </p>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4">
                <p class="font-extrabold text-blue-700">
                    Pencarian
                </p>
                <p class="text-slate-600 mt-1">
                    Menggunakan Sequential Search berdasarkan NIM dan nama praktikan.
                </p>
            </div>

            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                <p class="font-extrabold text-slate-700">
                    Pengurutan
                </p>
                <p class="text-slate-600 mt-1">
                    Menggunakan Merge Sort berdasarkan Prodi dan NIM.
                </p>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3">
                Terjadi kesalahan:
            </h2>

            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET"
              action="{{ route('koordinator.nilai-akhir') }}"
              class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Cari Praktikan
                </label>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari NIM atau nama"
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

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>

                <select name="prodi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Prodi</option>

                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Status Kelulusan
                </label>

                <select name="status_kelulusan"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="lulus" @selected(request('status_kelulusan') === 'lulus')>
                        Lulus
                    </option>
                    <option value="tidak_lulus" @selected(request('status_kelulusan') === 'tidak_lulus')>
                        Tidak Lulus
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Urutan NIM
                </label>

                <select name="sort_direction"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="asc" @selected(($sortDirection ?? request('sort_direction', 'asc')) === 'asc')>
                        NIM kecil ke besar
                    </option>

                    <option value="desc" @selected(($sortDirection ?? request('sort_direction')) === 'desc')>
                        NIM besar ke kecil
                    </option>
                </select>
            </div>

            <div class="xl:col-span-5 flex flex-col sm:flex-row gap-3">
                <button type="submit"
                        class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan
                </button>

                <a href="{{ route('koordinator.nilai-akhir') }}"
                   class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-100 p-4">
            @if (request('prodi'))
                <p class="text-sm text-slate-600 mb-4">
                    Unduh nilai akhir untuk program studi:
                    <span class="font-extrabold text-slate-900">{{ request('prodi') }}</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('koordinator.nilai-akhir.export', request()->query()) }}"
                       class="inline-flex justify-center px-5 py-3 rounded-2xl bg-green-600 text-white font-extrabold hover:bg-green-700 transition">
                        Unduh CSV
                    </a>

                    <a href="{{ route('koordinator.nilai-akhir.export-pdf', request()->query()) }}"
                       class="inline-flex justify-center px-5 py-3 rounded-2xl bg-red-600 text-white font-extrabold hover:bg-red-700 transition">
                        Unduh PDF
                    </a>
                </div>
            @else
                <p class="text-sm text-slate-600">
                    Pilih program studi terlebih dahulu untuk mengunduh nilai akhir per prodi.
                </p>
            @endif
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Daftar Nilai Akhir
            </h2>

            <p class="text-slate-500 mt-1">
                Data dicari dengan Sequential Search, lalu diurutkan menggunakan Merge Sort berdasarkan prodi dan 3 angka terakhir NIM.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1450px] text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700">
                        <th class="p-3 text-center rounded-l-xl">No.</th>
                        <th class="p-3 text-center">NIM</th>
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
                        <th class="p-3 text-center">Huruf</th>
                        <th class="p-3 text-center rounded-r-xl">Kelulusan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($nilaiAkhirs as $nilai)
                        @php
                            $lulus = (float) ($nilai->nilai_angka ?? 0) >= 60;
                        @endphp

                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="p-3 text-center">
                                {{ $loop->iteration + ($nilaiAkhirs->currentPage() - 1) * $nilaiAkhirs->perPage() }}
                            </td>

                            <td class="p-3 text-center font-bold whitespace-nowrap">
                                {{ $nilai->praktikan->user->nim ?? '-' }}
                            </td>

                            <td class="p-3 text-center font-semibold">
                                {{ $nilai->praktikan->user->name ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $nilai->praktikan->user->prodi ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $nilai->praktikan->kelompok->nama_kelompok ?? '-' }}
                            </td>

                            <td class="p-3 text-center">{{ $nilai->kehadiran ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->laporan ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->responsi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->asistensi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $nilai->kemampuan_praktik ?? '-' }}</td>

                            <td class="p-3 text-center font-extrabold">
                                {{ $nilai->nilai_angka !== null ? number_format((float) $nilai->nilai_angka, 2) : '-' }}
                            </td>

                            <td class="p-3 text-center font-extrabold">
                                {{ $nilai->nilai_bobot ?? '-' }}
                            </td>

                            <td class="p-3 text-center font-extrabold">
                                {{ $nilai->nilai_huruf ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-extrabold {{ $lulus ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $lulus ? 'Lulus' : 'Tidak Lulus' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="p-5 text-center text-slate-500">
                                Data nilai akhir belum tersedia.
                            </td>
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