@extends('layouts.koordinator', ['title' => 'Beranda Koordinator'])

@section('content')
<div class="space-y-8">

    {{-- Header Halaman --}}
    <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Beranda Koordinator Praktikum
                </h1>
                <p class="text-slate-500 mt-1.5 text-sm sm:text-base leading-relaxed">
                    Pantau ringkasan praktikum, status pelaksanaan, dan tingkat kelulusan praktikan per periode.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold self-start md:self-auto">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Tahun {{ $selectedTahunAkademik ?? '-' }} • Semester {{ $selectedSemester ?? '-' }}
            </div>
        </div>
    </section>

    {{-- Filter Periode --}}
    <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
        <form method="GET"
              action="{{ route('dashboard.koordinator') }}"
              class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Tahun Akademik
                </label>

                <select name="tahun_akademik"
                        class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 focus:border-blue-600 focus:ring-blue-600">
                    @foreach ($tahunAkademikOptions as $tahunAkademik)
                        <option value="{{ $tahunAkademik }}" @selected($selectedTahunAkademik === $tahunAkademik)>
                            {{ $tahunAkademik }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Semester
                </label>

                <select name="semester"
                        class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 focus:border-blue-600 focus:ring-blue-600">
                    @foreach ($semesterOptions as $semester)
                        <option value="{{ $semester }}" @selected($selectedSemester === $semester)>
                            Semester {{ $semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2 sm:col-span-2 md:col-span-1">
                <button type="submit"
                        class="flex-1 px-5 py-2.5 rounded-2xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800 transition shadow-sm">
                    Terapkan Filter
                </button>

                <a href="{{ route('dashboard.koordinator') }}"
                   class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-600 text-sm font-bold hover:bg-slate-200 transition text-center">
                    Reset
                </a>
            </div>
        </form>

        <div class="mt-5 rounded-2xl bg-slate-50 border border-slate-200/70 p-4">
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                <span class="font-bold text-slate-800">Konteks Filter:</span>
                Data ditampilkan untuk <span class="font-semibold text-slate-800">Tahun Akademik {{ $selectedTahunAkademik ?? '-' }}</span> dan <span class="font-semibold text-slate-800">Semester {{ $selectedSemester ?? '-' }}</span>.
                Ringkasan di bawah memisahkan Fisika Dasar 1 dan Fisika Dasar 2 secara independen agar data tidak tercampur.
            </p>
        </div>
    </section>

    {{-- Total Gabungan Sesuai Filter (Muted Neutral Cards) --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Periode Aktif</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2">
                {{ $totalRingkasan['totalPeriode'] }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">Praktikum terdaftar</p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Praktikan</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2">
                {{ $totalRingkasan['totalPraktikan'] }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">Mahasiswa terdaftar</p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Asisten</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2">
                {{ $totalRingkasan['totalAsisten'] }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">Asisten aktif</p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Kelompok</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2">
                {{ $totalRingkasan['totalKelompok'] }}
            </h3>
            <p class="text-xs text-slate-400 mt-1">Kelompok praktikum</p>
        </div>
    </section>

    {{-- Ringkasan Per Jenis Praktikum --}}
    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @foreach ($ringkasanPraktikums as $ringkasan)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
                
                {{-- Header Kartu Praktikum --}}
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $ringkasan['praktikum']?->is_aktif ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                                    {{ $ringkasan['label'] }}
                                </h2>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                                @if ($ringkasan['praktikum'])
                                    Tahun Akademik {{ $ringkasan['praktikum']->tahun_akademik }} • Semester {{ $ringkasan['praktikum']->semester }}
                                @else
                                    Periode belum tersedia pada filter ini.
                                @endif
                            </p>
                        </div>

                        @if ($ringkasan['praktikum']?->is_aktif)
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold self-start sm:self-auto">
                                ● Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-500 text-xs font-bold self-start sm:self-auto">
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-6 flex-1">
                    
                    {{-- Mini General Stats (Neutral, Muted) --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rounded-2xl bg-slate-50/80 border border-slate-200/60 p-3.5 text-center">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Praktikan</p>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1">
                                {{ $ringkasan['totalPraktikan'] }}
                            </h3>
                        </div>

                        <div class="rounded-2xl bg-slate-50/80 border border-slate-200/60 p-3.5 text-center">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Prodi</p>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1">
                                {{ $ringkasan['totalProdi'] }}
                            </h3>
                        </div>

                        <div class="rounded-2xl bg-slate-50/80 border border-slate-200/60 p-3.5 text-center">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Asisten</p>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1">
                                {{ $ringkasan['totalAsisten'] }}
                            </h3>
                        </div>

                        <div class="rounded-2xl bg-slate-50/80 border border-slate-200/60 p-3.5 text-center">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kelompok</p>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1">
                                {{ $ringkasan['totalKelompok'] }}
                            </h3>
                        </div>
                    </div>

                    {{-- SPOTLIGHT SECTION: PERSENTASE KELULUSAN (Fokus Utama) --}}
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-50/70 via-white to-teal-50/40 border-2 border-emerald-200/80 p-5 shadow-sm relative overflow-hidden">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100/80 text-emerald-800 text-xs font-extrabold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                    Tingkat Kelulusan
                                </span>
                                <h4 class="text-sm font-semibold text-slate-600 mt-1.5">
                                    Persentase Praktikan Lulus
                                </h4>
                            </div>

                            <div class="text-left sm:text-right">
                                <div class="text-3xl sm:text-4xl font-black text-emerald-700 tracking-tight">
                                    {{ $ringkasan['persentaseLulus'] }}%
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $ringkasan['totalLulus'] }} dari {{ $ringkasan['totalDinilai'] }} praktikan dinilai
                                </p>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-4">
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200/60">
                                <div class="bg-emerald-600 h-full rounded-full transition-all duration-700 ease-out"
                                     style="width: {{ min(100, max(0, $ringkasan['persentaseLulus'])) }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Nilai & Visualisasi Donut Chart --}}
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        
                        {{-- Donut Chart Container --}}
                        <div class="md:col-span-5 bg-slate-50/60 rounded-2xl border border-slate-200/60 p-4 flex flex-col items-center justify-center min-h-[200px]">
                            @if ($ringkasan['totalDinilai'] > 0)
                                <div class="w-full h-44 relative">
                                    <canvas id="kelulusanChart-{{ $ringkasan['key'] }}"></canvas>
                                </div>
                            @else
                                <div class="h-44 flex flex-col items-center justify-center text-center p-4 text-slate-400">
                                    <span class="text-3xl mb-2">📊</span>
                                    <p class="text-xs font-medium">Data nilai akhir belum tersedia</p>
                                </div>
                            @endif
                        </div>

                        {{-- Breakdown Items (Muted Neutral Cards dengan Dot Indicator) --}}
                        <div class="md:col-span-7 space-y-2.5">
                            
                            {{-- Total Dinilai --}}
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                    <span class="text-xs font-bold text-slate-600">Total Dinilai</span>
                                </div>
                                <span class="text-base sm:text-lg font-extrabold text-slate-800">
                                    {{ $ringkasan['totalDinilai'] }}
                                </span>
                            </div>

                            {{-- Total Lulus --}}
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-xs font-bold text-slate-600">Lulus (Nilai ≥ 60)</span>
                                </div>
                                <span class="text-base sm:text-lg font-extrabold text-emerald-700">
                                    {{ $ringkasan['totalLulus'] }}
                                </span>
                            </div>

                            {{-- Belum/Tidak Lulus --}}
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                    <span class="text-xs font-bold text-slate-600">Belum / Tidak Lulus</span>
                                </div>
                                <span class="text-base sm:text-lg font-extrabold text-slate-700">
                                    {{ $ringkasan['totalTidakLulus'] }}
                                </span>
                            </div>

                        </div>
                    </div>

                    {{-- Catatan / Keterangan --}}
                    <div class="rounded-xl bg-slate-50/70 border border-slate-200/50 p-3 text-xs text-slate-500 leading-relaxed">
                        <span class="font-bold text-slate-700">Catatan:</span>
                        Kelulusan dihitung berdasarkan ambang batas nilai akhir angka minimal 60 (Huruf Mutu C).
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
                        backgroundColor: ['#10b981', '#cbd5e1'],
                        hoverBackgroundColor: ['#059669', '#94a3b8'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 12,
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection