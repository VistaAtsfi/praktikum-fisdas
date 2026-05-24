@extends('layouts.koordinator', ['title' => 'Detail Periode Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">
                    Detail Periode Praktikum
                </h1>

                <p class="text-slate-500 mt-2">
                    {{ $praktikum->nama }} · {{ $praktikum->tahun_akademik }} · {{ $praktikum->semester }}
                </p>
            </div>

            <a href="{{ route('koordinator.periode') }}"
               class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-center">
                Kembali
            </a>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="POST" action="{{ route('koordinator.periode.update', $praktikum) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Praktikum</label>
                <input type="text" name="nama" value="{{ $praktikum->nama }}" required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Praktikum</label>
                <input type="text" name="jenis_praktikum" value="{{ $praktikum->jenis_praktikum }}" required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tahun Akademik</label>
                <input type="text" name="tahun_akademik" value="{{ $praktikum->tahun_akademik }}" required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Semester</label>
                <select name="semester" required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="Ganjil" @selected($praktikum->semester === 'Ganjil')>Ganjil</option>
                    <option value="Genap" @selected($praktikum->semester === 'Genap')>Genap</option>
                </select>
            </div>

            <label class="md:col-span-2 inline-flex items-center gap-2">
                <input type="checkbox" name="is_aktif" value="1"
                       @checked($praktikum->is_aktif)
                       class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                <span class="text-sm font-semibold text-slate-700">Jadikan periode aktif</span>
            </label>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Jumlah Program Studi</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $jumlahProgramStudi }}</h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Jumlah Praktikan</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $jumlahPraktikan }}</h3>
            <p class="text-xs text-slate-500 mt-2">L: {{ $praktikanLaki }} · P: {{ $praktikanPerempuan }}</p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Jumlah Asisten</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $jumlahAsisten }}</h3>
            <p class="text-xs text-slate-500 mt-2">L: {{ $asistenLaki }} · P: {{ $asistenPerempuan }}</p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Pertemuan Berjalan</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">{{ $pertemuanBerjalan }} / {{ $totalPertemuan }}</h3>
        </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <h2 class="text-xl font-extrabold text-slate-900 mb-4">
                Praktikan Berdasarkan Jenis Kelamin
            </h2>
            <div class="h-72">
                <canvas id="praktikanGenderChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <h2 class="text-xl font-extrabold text-slate-900 mb-4">
                Asisten Berdasarkan Jenis Kelamin
            </h2>
            <div class="h-72">
                <canvas id="asistenGenderChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <h2 class="text-xl font-extrabold text-slate-900 mb-4">
                Kelompok Praktikum per Program Studi
            </h2>
            <div class="h-72">
                <canvas id="kelompokProdiChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <h2 class="text-xl font-extrabold text-slate-900 mb-4">
                Rata-rata Nilai Akhir Praktikan
            </h2>
            <div class="h-72">
                <canvas id="nilaiAkhirChart"></canvas>
            </div>

            <div class="mt-4 text-center">
                <p class="text-sm text-slate-500">Rata-rata Nilai Akhir</p>
                <p class="text-4xl font-extrabold text-slate-900">{{ number_format($rataNilaiAkhir, 2) }} / 100</p>
            </div>
        </div>
    </section>

</div>

@php
    $praktikanGenderLabels = $chartPraktikanGenderLabels;
    $praktikanGenderData = $chartPraktikanGenderData;

    $asistenGenderLabels = $chartAsistenGenderLabels;
    $asistenGenderData = $chartAsistenGenderData;

    $kelompokLabels = $chartKelompokProdiLabels;
    $kelompokData = $chartKelompokProdiData;

    $nilaiLabels = $chartNilaiAkhirLabels;
    $nilaiData = $chartNilaiAkhirData;
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function makeDoughnut(id, labels, data, colors) {
        const canvas = document.getElementById(id);

        if (!canvas) {
            return;
        }

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
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

    makeDoughnut(
        'praktikanGenderChart',
        {!! json_encode($praktikanGenderLabels) !!},
        {!! json_encode($praktikanGenderData) !!},
        ['#2563eb', '#ec4899']
    );

    makeDoughnut(
        'asistenGenderChart',
        {!! json_encode($asistenGenderLabels) !!},
        {!! json_encode($asistenGenderData) !!},
        ['#0f766e', '#f97316']
    );

    makeDoughnut(
        'kelompokProdiChart',
        {!! json_encode($kelompokLabels) !!},
        {!! json_encode($kelompokData) !!},
        ['#7c3aed', '#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#0891b2']
    );

    makeDoughnut(
        'nilaiAkhirChart',
        {!! json_encode($nilaiLabels) !!},
        {!! json_encode($nilaiData) !!},
        ['#16a34a', '#e5e7eb']
    );
</script>
@endsection