@extends('layouts.koordinator', ['title' => 'Nilai Akhir'])

@section('content')
@php
    $frekuensiNilaiHuruf = collect($frekuensiNilaiHuruf ?? [
        'A' => 0,
        'B' => 0,
        'C' => 0,
        'D' => 0,
        'E' => 0,
    ]);

    $totalNilaiHuruf = $totalNilaiHuruf ?? $frekuensiNilaiHuruf->sum();

    $chartHurufLabels = collect($chartNilaiHurufLabels ?? $frekuensiNilaiHuruf->keys()->values());
    $chartHurufData = collect($chartNilaiHurufData ?? $frekuensiNilaiHuruf->values());

    $nilaiTerbanyakHuruf = $frekuensiNilaiHuruf->sortDesc()->keys()->first();
    $nilaiTerbanyakJumlah = $frekuensiNilaiHuruf->max() ?? 0;
@endphp

<div class="space-y-8 max-w-full overflow-x-hidden">

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
                    Menggunakan Merge Sort berdasarkan prodi dan NIM.
                </p>
            </div>
        </div>
    </section>

    @if (isset($errors) && $errors->any())
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
              class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">

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

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Tampilkan Isi
                </label>

                <select name="per_page"
                        onchange="this.form.submit()"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-blue-50/50 font-bold text-blue-900">
                    <option value="10" @selected(($perPage ?? request('per_page', 10)) == 10)>10 Baris</option>
                    <option value="50" @selected(($perPage ?? request('per_page')) == 50)>50 Baris</option>
                    <option value="100" @selected(($perPage ?? request('per_page')) == 100)>100 Baris</option>
                </select>
            </div>

            <div class="xl:col-span-6 flex flex-col sm:flex-row gap-3">
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

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden max-w-full">
        <div class="p-6 border-b border-blue-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">
                    Daftar Nilai Akhir
                </h2>

                <p class="text-slate-500 mt-1">
                    Data dicari dengan Sequential Search, lalu diurutkan menggunakan Merge Sort berdasarkan prodi dan NIM.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center px-4 py-2 rounded-2xl bg-blue-50 text-blue-700 font-bold text-xs border border-blue-100">
                    Menampilkan {{ $nilaiAkhirs->firstItem() ?? 0 }} - {{ $nilaiAkhirs->lastItem() ?? 0 }} dari {{ $nilaiAkhirs->total() }} praktikan
                </span>
            </div>
        </div>

        <div class="p-6 max-w-full overflow-hidden">
            {{-- Tampilan desktop --}}
            <div class="hidden xl:block">
                <table class="w-full table-fixed text-[11px]">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="p-2 text-center rounded-l-xl">No.</th>
                            <th class="p-2 text-center">NIM</th>
                            <th class="p-2 text-center">Nama</th>
                            <th class="p-2 text-center">Prodi</th>
                            <th class="p-2 text-center">Kelompok</th>
                            <th class="p-2 text-center">Kehadiran</th>
                            <th class="p-2 text-center">Laporan</th>
                            <th class="p-2 text-center">Responsi</th>
                            <th class="p-2 text-center">Asistensi</th>
                            <th class="p-2 text-center">Praktik</th>
                            <th class="p-2 text-center">Angka</th>
                            <th class="p-2 text-center">Bobot</th>
                            <th class="p-2 text-center">Huruf</th>
                            <th class="p-2 text-center rounded-r-xl">Kelulusan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($nilaiAkhirs as $nilai)
                            @php
                                $huruf = strtoupper(trim((string) ($nilai->nilai_huruf ?? '')));
                                $statusKelulusan = $huruf === ''
                                    ? '-'
                                    : ($huruf === 'E' ? 'Tidak Lulus' : 'Lulus');

                                $statusClass = match ($statusKelulusan) {
                                    'Lulus' => 'bg-green-100 text-green-700',
                                    'Tidak Lulus' => 'bg-red-100 text-red-700',
                                    default => 'bg-slate-100 text-slate-500',
                                };
                            @endphp

                            <tr class="hover:bg-blue-50/40 transition align-top">
                                <td class="p-2 text-center">
                                    {{ $loop->iteration + ($nilaiAkhirs->currentPage() - 1) * $nilaiAkhirs->perPage() }}
                                </td>

                                <td class="p-2 text-center font-bold break-words">
                                    {{ $nilai->praktikan->user->nim ?? '-' }}
                                </td>

                                <td class="p-2 text-center font-semibold break-words">
                                    {{ $nilai->praktikan->user->name ?? '-' }}
                                </td>

                                <td class="p-2 text-center break-words">
                                    {{ $nilai->praktikan->user->prodi ?? '-' }}
                                </td>

                                <td class="p-2 text-center break-words">
                                    {{ $nilai->praktikan->kelompok->nama_kelompok ?? '-' }}
                                </td>

                                <td class="p-2 text-center">
                                    {{ $nilai->kehadiran ?? $nilai->praktikan?->nilaiHarian?->rata_rata_kehadiran ?? '-' }}
                                </td>

                                <td class="p-2 text-center">
                                    {{ $nilai->laporan ?? $nilai->praktikan?->nilaiHarian?->laporan ?? $nilai->praktikan?->nilaiPenulisanLaporan?->nilai_akhir ?? '-' }}
                                </td>

                                <td class="p-2 text-center">
                                    {{ $nilai->responsi ?? $nilai->praktikan?->nilaiHarian?->rata_rata_responsi ?? '-' }}
                                </td>

                                <td class="p-2 text-center">
                                    {{ $nilai->asistensi ?? $nilai->praktikan?->nilaiHarian?->rata_rata_asistensi ?? '-' }}
                                </td>

                                <td class="p-2 text-center">
                                    {{ $nilai->kemampuan_praktik ?? $nilai->praktikan?->nilaiHarian?->rata_rata_kemampuan_praktik ?? $nilai->praktikan?->nilaiKemampuanPraktikum?->rata_rata_akhir ?? '-' }}
                                </td>

                                <td class="p-2 text-center font-extrabold">
                                    {{ $nilai->nilai_angka !== null ? number_format((float) $nilai->nilai_angka, 2) : '-' }}
                                </td>

                                <td class="p-2 text-center font-extrabold">
                                    {{ $nilai->nilai_bobot ?? '-' }}
                                </td>

                                <td class="p-2 text-center font-extrabold">
                                    {{ $nilai->nilai_huruf ?? '-' }}
                                </td>

                                <td class="p-2 text-center">
                                    <span class="inline-flex justify-center px-2 py-1 rounded-full text-[10px] font-extrabold {{ $statusClass }}">
                                        {{ $statusKelulusan }}
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
            </div>

            {{-- Tampilan tablet/mobile: turun ke bawah, tidak scroll samping --}}
            <div class="xl:hidden space-y-4">
                @forelse ($nilaiAkhirs as $nilai)
                    @php
                        $huruf = strtoupper(trim((string) ($nilai->nilai_huruf ?? '')));
                        $statusKelulusan = $huruf === ''
                            ? '-'
                            : ($huruf === 'E' ? 'Tidak Lulus' : 'Lulus');

                        $statusClass = match ($statusKelulusan) {
                            'Lulus' => 'bg-green-100 text-green-700',
                            'Tidak Lulus' => 'bg-red-100 text-red-700',
                            default => 'bg-slate-100 text-slate-500',
                        };
                    @endphp

                    <div class="rounded-3xl border border-blue-100 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs text-slate-500">
                                    {{ $nilai->praktikan->user->nim ?? '-' }}
                                </p>

                                <h3 class="text-lg font-extrabold text-slate-900 mt-1">
                                    {{ $nilai->praktikan->user->name ?? '-' }}
                                </h3>

                                <p class="text-sm text-slate-500 mt-1">
                                    {{ $nilai->praktikan->user->prodi ?? '-' }}
                                </p>
                            </div>

                            <span class="shrink-0 px-3 py-1 rounded-full text-xs font-extrabold {{ $statusClass }}">
                                {{ $statusKelulusan }}
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-slate-500">Kelompok</p>
                                <p class="font-bold text-slate-900 mt-1">
                                    {{ $nilai->praktikan->kelompok->nama_kelompok ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-slate-500">Nilai Angka</p>
                                <p class="font-extrabold text-slate-900 mt-1">
                                    {{ $nilai->nilai_angka !== null ? number_format((float) $nilai->nilai_angka, 2) : '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-slate-500">Nilai Huruf</p>
                                <p class="font-extrabold text-slate-900 mt-1">
                                    {{ $nilai->nilai_huruf ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-blue-50 p-3">
                                <p class="text-blue-700">Kehadiran</p>
                                <p class="font-bold text-slate-900 mt-1">
                                    {{ $nilai->kehadiran ?? $nilai->praktikan?->nilaiHarian?->rata_rata_kehadiran ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-blue-50 p-3">
                                <p class="text-blue-700">Laporan</p>
                                <p class="font-bold text-slate-900 mt-1">
                                    {{ $nilai->laporan ?? $nilai->praktikan?->nilaiHarian?->laporan ?? $nilai->praktikan?->nilaiPenulisanLaporan?->nilai_akhir ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-blue-50 p-3">
                                <p class="text-blue-700">Responsi</p>
                                <p class="font-bold text-slate-900 mt-1">
                                    {{ $nilai->responsi ?? $nilai->praktikan?->nilaiHarian?->rata_rata_responsi ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-purple-50 p-3">
                                <p class="text-purple-700">Asistensi</p>
                                <p class="font-bold text-slate-900 mt-1">
                                    {{ $nilai->asistensi ?? $nilai->praktikan?->nilaiHarian?->rata_rata_asistensi ?? '-' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-cyan-50 p-3 sm:col-span-2">
                                <p class="text-cyan-700">Kemampuan Praktik</p>
                                <p class="font-bold text-slate-900 mt-1">
                                    {{ $nilai->kemampuan_praktik ?? $nilai->praktikan?->nilaiHarian?->rata_rata_kemampuan_praktik ?? $nilai->praktikan?->nilaiKemampuanPraktikum?->rata_rata_akhir ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center text-slate-500">
                        Data nilai akhir belum tersedia.
                    </div>
                @endforelse
            </div>

            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-slate-100 pt-4">
                <p class="text-xs text-slate-500 font-semibold">
                    Halaman {{ $nilaiAkhirs->currentPage() }} dari {{ $nilaiAkhirs->lastPage() }} (Total {{ $nilaiAkhirs->total() }} praktikan)
                </p>

                <div>
                    {{ $nilaiAkhirs->links() }}
                </div>
            </div>
        </div>
    </section>

    {{-- Visualisasi Nilai Huruf --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden max-w-full">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Visualisasi Nilai Huruf
            </h2>

            <p class="text-slate-500 mt-1">
                Grafik ini menampilkan distribusi nilai huruf A, B, C, D, dan E berdasarkan filter yang dipilih.
            </p>
        </div>

        <div class="p-6 grid grid-cols-1 xl:grid-cols-12 gap-6 max-w-full overflow-hidden">
            <div class="xl:col-span-4 space-y-4">
                <div class="rounded-3xl bg-slate-50 border border-slate-100 p-5">
                    <h3 class="text-xl font-extrabold text-slate-900">
                        Frekuensi Nilai
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Jumlah praktikan pada setiap nilai huruf.
                    </p>

                    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-100 text-slate-700">
                                    <th class="p-3 text-center">Nilai</th>
                                    <th class="p-3 text-center">Frekuensi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach (($frekuensiNilaiHuruf ?? collect(['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0])) as $huruf => $jumlah)
                                    <tr>
                                        <td class="p-3 text-center font-extrabold text-slate-900">
                                            {{ $huruf }}
                                        </td>

                                        <td class="p-3 text-center font-bold text-slate-700">
                                            {{ $jumlah }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Card kecil kanan-kiri --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4">
                        <p class="text-blue-700 font-extrabold text-xs">
                            Total Nilai Huruf
                        </p>

                        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">
                            {{ $totalNilaiHuruf ?? 0 }}
                        </h3>

                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Praktikan yang sudah memiliki nilai huruf.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-cyan-50 border border-cyan-100 p-4">
                        <p class="text-cyan-700 font-extrabold text-xs">
                            Nilai Terbanyak
                        </p>

                        <h3 class="text-3xl font-extrabold text-slate-900 mt-2">
                            {{ $nilaiTerbanyakHuruf ?? '-' }}
                        </h3>

                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            {{ $nilaiTerbanyakJumlah ?? 0 }} praktikan pada nilai ini.
                        </p>
                    </div>
                </div>
            </div>

            <div class="xl:col-span-8 rounded-3xl bg-slate-50 border border-slate-100 p-6">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">
                            Distribusi Nilai Huruf
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            Diagram menunjukkan perbandingan jumlah nilai A sampai E.
                        </p>
                    </div>
                </div>

                @if (($totalNilaiHuruf ?? 0) > 0)
                    <div class="h-96 mt-6">
                        <canvas id="nilaiHurufChart"></canvas>
                    </div>
                @else
                    <div class="h-96 mt-6 flex items-center justify-center text-center text-slate-500">
                        Data nilai huruf belum tersedia pada filter ini.
                    </div>
                @endif
            </div>
        </div>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const nilaiHurufCanvas = document.getElementById('nilaiHurufChart');

    if (nilaiHurufCanvas) {
        new Chart(nilaiHurufCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($chartHurufLabels),
                datasets: [{
                    data: @json($chartHurufData),
                    backgroundColor: [
                        '#2563eb',
                        '#22c55e',
                        '#f59e0b',
                        '#ef4444',
                        '#7f1d1d'
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    hoverOffset: 10,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '58%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            padding: 18,
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection