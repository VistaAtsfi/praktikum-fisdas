@extends('layouts.koordinator', ['title' => 'Data Praktikan'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Data Praktikan
        </h1>

        <p class="text-slate-500 mt-2">
            Lihat data praktikan yang terdaftar pada periode praktikum.
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
                    Data diurutkan menggunakan Merge Sort berdasarkan Prodi, lalu NIM.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET"
            action="{{ route('koordinator.praktikan') }}"
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Cari Praktikan
                </label>

                <input type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari NIM atau nama praktikan"
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

                    @foreach (($prodis ?? collect()) as $prodi)
                        <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Kelompok
                </label>

                <select name="kelompok_id"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kelompok</option>

                    @foreach (($kelompoks ?? collect()) as $kelompok)
                        <option value="{{ $kelompok->id }}" @selected(request('kelompok_id') == $kelompok->id)>
                            {{ $kelompok->nama_kelompok }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Sesi Praktikum
                </label>

                <select name="sesi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Sesi</option>

                    @foreach (($sesis ?? collect()) as $sesi)
                        <option value="{{ $sesi }}" @selected(request('sesi') === $sesi)>
                            {{ $sesi }}
                        </option>
                    @endforeach
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

            <div class="xl:col-span-2 flex flex-col sm:flex-row items-end gap-3">
                <button type="submit"
                        class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Cari
                </button>

                <a href="{{ route('koordinator.praktikan') }}"
                class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Daftar Praktikan
            </h2>

            <p class="text-slate-500 mt-1">
                Data ditampilkan berdasarkan hasil Sequential Search dan diurutkan dengan Merge Sort.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700">
                        <th class="p-3 text-center rounded-l-xl">No.</th>
                        <th class="p-3 text-center">NIM</th>
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
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="p-3 text-center">
                                {{ $loop->iteration + ($praktikans->currentPage() - 1) * $praktikans->perPage() }}
                            </td>

                            <td class="p-3 text-center font-bold whitespace-nowrap">
                                {{ $praktikan->user->nim ?? '-' }}
                            </td>

                            <td class="p-3 text-center font-semibold">
                                {{ $praktikan->user->name ?? '-' }}
                            </td>

                            <td class="p-3 text-center whitespace-nowrap">
                                {{ $praktikan->user->nomor_ponsel ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $praktikan->user->email ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $praktikan->user->prodi ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $praktikan->kelompok->nama_kelompok ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $praktikan->kelompok->sesi ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-5 text-center text-slate-500">
                                Data praktikan belum tersedia.
                            </td>
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