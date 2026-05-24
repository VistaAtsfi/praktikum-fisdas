@extends('layouts.koordinator', ['title' => 'Beranda Koordinator'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Beranda Koordinator Praktikum
        </h1>

        <p class="text-slate-500 mt-2">
            Pantau ringkasan periode praktikum, praktikan, asisten, kelompok, dan kelulusan.
        </p>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Periode Praktikum</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $totalPeriode }}</h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Praktikan Terdaftar</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $totalPraktikan }}</h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Asisten Aktif</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $totalAsisten }}</h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Kelompok</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $totalKelompok }}</h3>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Ringkasan Periode Aktif
            </h2>

            <p class="text-slate-500 mt-1">
                {{ $praktikumAktif?->nama ?? 'Belum ada periode aktif' }}
            </p>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-5 rounded-3xl bg-slate-50 border border-slate-100 p-6">
                <h3 class="text-xl font-extrabold text-slate-900 mb-4">
                    Presentase Kelulusan Praktikan
                </h3>

                @if ($totalDinilai > 0)
                    <div class="h-80">
                        <canvas id="kelulusanChart"></canvas>
                    </div>
                @else
                    <div class="h-80 flex items-center justify-center text-center text-slate-500">
                        Data nilai akhir belum tersedia.
                    </div>
                @endif
            </div>

            <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-3xl bg-blue-50 border border-blue-100 p-6">
                    <p class="text-blue-700 font-bold">Total Dinilai</p>
                    <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $totalDinilai }}</h3>
                </div>

                <div class="rounded-3xl bg-green-50 border border-green-100 p-6">
                    <p class="text-green-700 font-bold">Total Lulus</p>
                    <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $totalLulus }}</h3>
                </div>

                <div class="rounded-3xl bg-cyan-50 border border-cyan-100 p-6 md:col-span-2">
                    <p class="text-cyan-700 font-bold">Persentase Kelulusan</p>
                    <h3 class="text-5xl font-extrabold text-slate-900 mt-2">{{ $persentaseLulus }}%</h3>
                </div>
            </div>
        </div>
    </section>

</div>

@php
    $chartLabels = $chartKelulusanLabels ?? ['Lulus', 'Belum/Tidak Lulus'];
    $chartData = $chartKelulusanData ?? [0, 0];
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const kelulusanCanvas = document.getElementById('kelulusanChart');

    if (kelulusanCanvas) {
        new Chart(kelulusanCanvas, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    data: {!! json_encode($chartData) !!},
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
</script>
@endsection