@extends('layouts.asisten', ['title' => 'Penilaian Praktikan'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Penilaian Praktikan
        </h1>

        <p class="text-slate-500 mt-2">
            Kelola dan lihat rekap nilai praktikan.
        </p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('asisten.penilaian') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Cari Praktikan</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari NIM atau nama praktikan"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Kelompok</label>
                <select name="kelompok_id"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kelompok</option>
                    @foreach ($kelompoks as $kelompok)
                        <option value="{{ $kelompok->id }}" @selected(request('kelompok_id') == $kelompok->id)>
                            {{ $kelompok->nama_kelompok }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Status Nilai</label>
                <select name="status_nilai"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="sudah" @selected(request('status_nilai') === 'sudah')>Sudah Dinilai</option>
                    <option value="belum" @selected(request('status_nilai') === 'belum')>Belum Dinilai</option>
                </select>
            </div>

            <div class="md:col-span-3 flex gap-3">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan
                </button>

                <a href="{{ route('asisten.penilaian') }}"
                   class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Daftar Nilai Praktikan
            </h2>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">NIM</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">Kelompok</th>
                        <th class="p-3 text-center">Sesi</th>
                        <th class="p-3 text-center">Kehadiran</th>
                        <th class="p-3 text-center">Laporan</th>
                        <th class="p-3 text-center">Responsi</th>
                        <th class="p-3 text-center">Asistensi</th>
                        <th class="p-3 text-center">Kemampuan Praktik</th>
                        <th class="p-3 text-center">Nilai Angka</th>
                        <th class="p-3 text-center">Nilai Bobot</th>
                        <th class="p-3 text-center">Nilai Huruf</th>
                        <th class="p-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($praktikans as $praktikan)
                        <tr>
                            <td class="p-3 text-center">{{ $praktikan->user->nim ?? '-' }}</td>
                            <td class="p-3 text-center font-semibold">{{ $praktikan->user->name ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->kelompok->nama_kelompok ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->kelompok->sesi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->nilaiAkhir->kehadiran ?? $praktikan->nilaiHarian->rata_rata_kehadiran ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->nilaiAkhir->laporan ?? $praktikan->nilaiHarian->laporan ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->nilaiAkhir->responsi ?? $praktikan->nilaiHarian->rata_rata_responsi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->nilaiAkhir->asistensi ?? $praktikan->nilaiHarian->rata_rata_asistensi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $praktikan->nilaiAkhir->kemampuan_praktik ?? $praktikan->nilaiHarian->rata_rata_kemampuan_praktik ?? '-' }}</td>
                            <td class="p-3 text-center font-extrabold">
                                {{ $praktikan->nilaiAkhir->nilai_angka ?? '-' }}
                            </td>
                            <td class="p-3 text-center font-extrabold">
                                {{ $praktikan->nilaiAkhir->nilai_bobot ?? '-' }}
                            </td>
                            <td class="p-3 text-center font-extrabold">
                                {{ $praktikan->nilaiAkhir->nilai_huruf ?? '-' }}
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('asisten.penilaian.show', $praktikan) }}"
                                class="inline-flex px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                                    Beri Nilai
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="p-5 text-center text-slate-500">
                                Data praktikan belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if (method_exists($praktikans, 'links'))
                <div class="mt-6">
                    {{ $praktikans->links() }}
                </div>
            @endif
        </div>
    </section>

</div>
@endsection