@extends('layouts.koordinator', ['title' => 'Beranda Koordinator'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Beranda Koordinator Praktikum
        </h1>

        <p class="text-slate-500 mt-2">
            Pantau ringkasan praktikum berdasarkan tahun akademik, semester, dan jenis praktikum.
        </p>
    </section>

    {{-- Filter --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET"
              action="{{ route('dashboard.koordinator') }}"
              class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Tahun Akademik
                </label>

                <select name="tahun_akademik"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($tahunAkademikOptions as $tahunAkademik)
                        <option value="{{ $tahunAkademik }}" @selected($selectedTahunAkademik === $tahunAkademik)>
                            {{ $tahunAkademik }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Semester
                </label>

                <select name="semester"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($semesterOptions as $semester)
                        <option value="{{ $semester }}" @selected($selectedSemester === $semester)>
                            {{ $semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan
                </button>

                <a href="{{ route('dashboard.koordinator') }}"
                   class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-5 rounded-2xl bg-blue-50 border border-blue-100 p-4">
            <p class="text-sm text-blue-800 leading-relaxed">
                Data pada beranda ini ditampilkan berdasarkan
                <span class="font-extrabold">Tahun Akademik {{ $selectedTahunAkademik ?? '-' }}</span>
                dan
                <span class="font-extrabold">Semester {{ $selectedSemester ?? '-' }}</span>.
                Ringkasan dipisahkan antara Praktikum Fisika Dasar 1 dan Praktikum Fisika Dasar 2 agar jumlah praktikan, asisten, kelompok, dan kelulusan tidak tercampur.
            </p>
        </div>
    </section>

    {{-- Total Gabungan Sesuai Filter --}}
    <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Periode Pada Filter Ini</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $totalRingkasan['totalPeriode'] }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Praktikan</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $totalRingkasan['totalPraktikan'] }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Asisten Aktif</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $totalRingkasan['totalAsisten'] }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Kelompok</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $totalRingkasan['totalKelompok'] }}
            </h3>
        </div>
    </section>

    {{-- Ringkasan Per Jenis Praktikum --}}
    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @foreach ($ringkasanPraktikums as $ringkasan)
            <div class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-blue-100">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                        <div>
                            <h2 class="text-2xl font-extrabold text-slate-900">
                                {{ $ringkasan['label'] }}
                            </h2>

                            <p class="text-slate-500 mt-1">
                                @if ($ringkasan['praktikum'])
                                    {{ $ringkasan['praktikum']->tahun_akademik }}
                                    -
                                    Semester {{ $ringkasan['praktikum']->semester }}
                                @else
                                    Periode belum tersedia pada filter ini.
                                @endif
                            </p>
                        </div>

                        @if ($ringkasan['praktikum']?->is_aktif)
                            <span class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-extrabold">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-extrabold">
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="rounded-2xl bg-blue-50 border border-blue-100 p-4">
                            <p class="text-xs font-bold text-blue-700">Praktikan</p>
                            <h3 class="text-3xl font-extrabold text-slate-900 mt-2">
                                {{ $ringkasan['totalPraktikan'] }}
                            </h3>
                        </div>

                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                            <p class="text-xs font-bold text-slate-600">Program Studi</p>
                            <h3 class="text-3xl font-extrabold text-slate-900 mt-2">
                                {{ $ringkasan['totalProdi'] }}
                            </h3>
                        </div>

                        <div class="rounded-2xl bg-purple-50 border border-purple-100 p-4">
                            <p class="text-xs font-bold text-purple-700">Asisten</p>
                            <h3 class="text-3xl font-extrabold text-slate-900 mt-2">
                                {{ $ringkasan['totalAsisten'] }}
                            </h3>
                        </div>

                        <div class="rounded-2xl bg-cyan-50 border border-cyan-100 p-4">
                            <p class="text-xs font-bold text-cyan-700">Kelompok</p>
                            <h3 class="text-3xl font-extrabold text-slate-900 mt-2">
                                {{ $ringkasan['totalKelompok'] }}
                            </h3>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                        <div class="lg:col-span-5 rounded-3xl bg-slate-50 border border-slate-100 p-5">
                            <h3 class="text-lg font-extrabold text-slate-900">
                                Persentase Kelulusan
                            </h3>

                            @if ($ringkasan['totalDinilai'] > 0)
                                <div class="h-64 mt-4">
                                    <canvas id="kelulusanChart-{{ $ringkasan['key'] }}"></canvas>
                                </div>
                            @else
                                <div class="h-64 mt-4 flex items-center justify-center text-center text-slate-500">
                                    Data nilai akhir belum tersedia.
                                </div>
                            @endif
                        </div>

                        <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">
                                <p class="text-blue-700 font-bold">Total Dinilai</p>
                                <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                                    {{ $ringkasan['totalDinilai'] }}
                                </h3>
                            </div>

                            <div class="rounded-2xl bg-green-50 border border-green-100 p-5">
                                <p class="text-green-700 font-bold">Total Lulus</p>
                                <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                                    {{ $ringkasan['totalLulus'] }}
                                </h3>
                            </div>

                            <div class="rounded-2xl bg-red-50 border border-red-100 p-5">
                                <p class="text-red-700 font-bold">Belum/Tidak Lulus</p>
                                <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                                    {{ $ringkasan['totalTidakLulus'] }}
                                </h3>
                            </div>

                            <div class="rounded-2xl bg-cyan-50 border border-cyan-100 p-5">
                                <p class="text-cyan-700 font-bold">Kelulusan</p>
                                <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                                    {{ $ringkasan['persentaseLulus'] }}%
                                </h3>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                        <p class="text-sm text-slate-600 leading-relaxed">
                            <span class="font-extrabold text-slate-800">Keterangan:</span>
                            Total dinilai hanya menghitung praktikan yang sudah memiliki nilai akhir.
                            Status lulus dihitung dari nilai angka minimal 60.
                            Data ini hanya untuk {{ $ringkasan['label'] }} pada tahun akademik dan semester yang dipilih.
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

</div>

@php
    $chartRows = $ringkasanPraktikums->map(function ($ringkasan) {
        return [
            'id' => 'kelulusanChart-' . $ringkasan['key'],
            'labels' => $ringkasan['chartLabels'],
            'data' => $ringkasan['chartData'],
            'totalDinilai' => $ringkasan['totalDinilai'],
        ];
    })->values();
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartRows = @json($chartRows);

    chartRows.forEach((row) => {
        const canvas = document.getElementById(row.id);

        if (canvas && row.totalDinilai > 0) {
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: row.labels,
                    datasets: [{
                        data: row.data,
                        backgroundColor: ['#16a34a', '#dc2626'],
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }
    });
</script>
@endsection