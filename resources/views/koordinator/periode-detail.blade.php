@extends('layouts.koordinator', ['title' => 'Detail Periode Praktikum'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Detail Periode Praktikum
                </h1>

                <p class="text-slate-500 mt-1.5 text-sm sm:text-base leading-relaxed">
                    {{ $praktikum->nama }} · Tahun Akademik {{ $praktikum->tahun_akademik }} · Semester {{ $praktikum->semester }}
                </p>
            </div>

            <a href="{{ route('koordinator.periode') }}"
               class="px-5 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-extrabold hover:bg-slate-200 transition text-center self-start lg:self-auto">
                Kembali
            </a>
        </div>
    </section>

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3 text-sm">
                Terjadi kesalahan input:
            </h2>

            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6">
        <h2 class="text-xl font-extrabold text-slate-900 mb-5">
            Ubah Periode Praktikum
        </h2>

        <form method="POST"
              action="{{ route('koordinator.periode.update', $praktikum) }}"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Nama Praktikum
                </label>

                <select name="nama"
                        id="nama_praktikum"
                        required
                        class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 focus:border-blue-600 focus:ring-blue-600">
                    <option value="">Pilih Nama Praktikum</option>

                    <option value="Praktikum Fisika Dasar 1"
                        @selected(old('nama', $praktikum->nama) === 'Praktikum Fisika Dasar 1')>
                        Praktikum Fisika Dasar 1
                    </option>

                    <option value="Praktikum Fisika Dasar 2"
                        @selected(old('nama', $praktikum->nama) === 'Praktikum Fisika Dasar 2')>
                        Praktikum Fisika Dasar 2
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Jenis Praktikum
                </label>

                <select name="jenis_praktikum"
                        id="jenis_praktikum"
                        required
                        class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 focus:border-blue-600 focus:ring-blue-600">
                    <option value="">Pilih Jenis Praktikum</option>

                    <option value="fisdas_1"
                        @selected(old('jenis_praktikum', $praktikum->jenis_praktikum) === 'fisdas_1')>
                        Fisdas 1
                    </option>

                    <option value="fisdas_2"
                        @selected(old('jenis_praktikum', $praktikum->jenis_praktikum) === 'fisdas_2')>
                        Fisdas 2
                    </option>
                </select>

                <p class="text-xs text-slate-400 mt-2">
                    Jenis praktikum dipakai sebagai kode internal sistem.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Tahun Akademik
                </label>

                <input type="text"
                       name="tahun_akademik"
                       value="{{ old('tahun_akademik', $praktikum->tahun_akademik) }}"
                       required
                       pattern="[0-9]{4}/[0-9]{4}"
                       placeholder="2025/2026"
                       class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 focus:border-blue-600 focus:ring-blue-600">

                <p class="text-xs text-slate-400 mt-2">
                    Gunakan format tahun/tahun, contoh: 2025/2026.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                    Semester
                </label>

                <select name="semester"
                        required
                        class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 focus:border-blue-600 focus:ring-blue-600">
                    <option value="">Pilih Semester</option>

                    <option value="Ganjil" @selected(old('semester', $praktikum->semester) === 'Ganjil')>
                        Ganjil
                    </option>

                    <option value="Genap" @selected(old('semester', $praktikum->semester) === 'Genap')>
                        Genap
                    </option>
                </select>
            </div>

            <label class="md:col-span-2 inline-flex items-center gap-2">
                <input type="checkbox"
                       name="is_aktif"
                       value="1"
                       @checked(old('is_aktif', $praktikum->is_aktif))
                       class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">

                <span class="text-sm font-semibold text-slate-700">
                    Jadikan periode aktif
                </span>
            </label>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                        class="px-5 py-2.5 rounded-2xl bg-blue-700 text-white text-sm font-bold hover:bg-blue-800 transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    {{-- Ringkasan Metrik Periode --}}
    <section class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition flex flex-col justify-between text-center">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Program Studi</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                    {{ $jumlahProgramStudi }}
                </h3>
            </div>
            <p class="text-xs text-slate-400 mt-2 font-medium">Total jurusan terdaftar</p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition flex flex-col justify-between text-center">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Praktikan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                    {{ $jumlahPraktikan }}
                </h3>
            </div>
            <p class="text-xs text-slate-400 mt-2 font-medium">L: {{ $praktikanLaki }} · P: {{ $praktikanPerempuan }}</p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition flex flex-col justify-between text-center">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Asisten</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                    {{ $jumlahAsisten }}
                </h3>
            </div>
            <p class="text-xs text-slate-400 mt-2 font-medium">L: {{ $asistenLaki }} · P: {{ $asistenPerempuan }}</p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition flex flex-col justify-between text-center">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Kelompok</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                    {{ $totalKelompok }}
                </h3>
            </div>
            <p class="text-xs text-slate-400 mt-2 font-medium">Gabungan semua prodi</p>
        </div>

        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm p-5 hover:border-slate-300 transition col-span-2 md:col-span-1 xl:col-span-1 flex flex-col justify-between text-center">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pertemuan Berjalan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-2 tracking-tight">
                    {{ $pertemuanBerjalan }} <span class="text-base sm:text-lg font-normal text-slate-400">/ {{ $totalPertemuan }}</span>
                </h3>
            </div>
            <p class="text-xs text-slate-400 mt-2 font-medium">Modul terlaksana</p>
        </div>
    </section>

    {{-- Tabel Kelompok per Prodi --}}
    <section class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xl font-extrabold text-slate-900">
                Total Kelompok per Program Studi
            </h2>

            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Jumlah kelompok dihitung berdasarkan kelompok praktikan pada setiap program studi.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[500px] text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700">
                        <th class="p-3 text-center font-bold rounded-l-xl">Program Studi</th>
                        <th class="p-3 text-center font-bold rounded-r-xl">Jumlah Kelompok</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($kelompokPerProdi as $prodi => $jumlah)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 text-center font-semibold text-slate-800">
                                {{ $prodi }}
                            </td>

                            <td class="p-3 text-center font-bold text-slate-700">
                                {{ $jumlah }} kelompok
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="p-5 text-center text-slate-500">
                                Data kelompok per program studi belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <tr class="bg-slate-50 border-t border-slate-200">
                        <td class="p-3 text-center font-extrabold text-slate-900">
                            Total Kelompok
                        </td>

                        <td class="p-3 text-center font-extrabold text-slate-900">
                            {{ $totalKelompok }} kelompok
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    {{-- Visualisasi Charts --}}
    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">
                    Praktikan Berdasarkan Jenis Kelamin
                </h2>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">Total {{ $jumlahPraktikan }}</span>
            </div>

            <div class="h-72 relative flex items-center justify-center">
                <canvas id="praktikanGenderChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">
                    Asisten Berdasarkan Jenis Kelamin
                </h2>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">Total {{ $jumlahAsisten }}</span>
            </div>

            <div class="h-72 relative flex items-center justify-center">
                <canvas id="asistenGenderChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">
                    Kelompok Praktikum per Program Studi
                </h2>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">Total {{ $totalKelompok }}</span>
            </div>

            <div class="h-72 relative flex items-center justify-center">
                <canvas id="kelompokProdiChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">
                    Rata-rata Nilai Akhir Praktikan
                </h2>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">Skala 0 - 100</span>
            </div>

            <div class="h-72 relative flex items-center justify-center">
                <canvas id="nilaiAkhirChart"></canvas>
            </div>
        </div>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const namaPraktikum = document.getElementById('nama_praktikum');
    const jenisPraktikum = document.getElementById('jenis_praktikum');

    if (namaPraktikum && jenisPraktikum) {
        namaPraktikum.addEventListener('change', function () {
            if (this.value === 'Praktikum Fisika Dasar 1') {
                jenisPraktikum.value = 'fisdas_1';
            }

            if (this.value === 'Praktikum Fisika Dasar 2') {
                jenisPraktikum.value = 'fisdas_2';
            }
        });
    }

    function makeDoughnut(id, labels, data, colors, centerValue, centerLabel) {
        const canvas = document.getElementById(id);

        if (!canvas) {
            return;
        }

        const total = (data || []).reduce((sum, val) => sum + (Number(val) || 0), 0);
        if (total === 0) {
            const container = canvas.parentElement;
            if (container) {
                container.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-center p-4 text-slate-400">
                        <span class="text-3xl mb-2">📊</span>
                        <p class="text-xs font-semibold text-slate-400">Data belum tersedia</p>
                    </div>
                `;
            }
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
                    borderWidth: 2,
                    hoverBorderColor: '#ffffff',
                    hoverBorderWidth: 2,
                    hoverOffset: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                layout: {
                    padding: {
                        top: 8,
                        bottom: 8,
                        left: 8,
                        right: 8
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 12,
                            font: {
                                size: 11,
                                weight: '600',
                                family: "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
                            },
                            color: '#64748b'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const val = Number(context.raw) || 0;
                                if (id === 'nilaiAkhirChart') {
                                    return ` ${label}: ${val.toFixed(2)}`;
                                }
                                const sum = context.dataset.data.reduce((a, b) => a + (Number(b) || 0), 0);
                                const pct = sum > 0 ? ((val / sum) * 100).toFixed(1) : '0';
                                return ` ${label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                }
            },
            plugins: [{
                id: 'centerTextPlugin_' + id,
                beforeDraw(chart) {
                    if (!centerValue) return;
                    const { ctx } = chart;
                    const meta = chart.getDatasetMeta(0);
                    if (!meta || !meta.data || meta.data.length === 0) return;
                    
                    const centerX = meta.data[0].x;
                    const centerY = meta.data[0].y;
                    
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    
                    ctx.font = "800 20px system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
                    ctx.fillStyle = "#1e293b";
                    ctx.fillText(centerValue, centerX, centerY - (centerLabel ? 7 : 0));
                    
                    if (centerLabel) {
                        ctx.font = "700 10px system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
                        ctx.fillStyle = "#94a3b8";
                        ctx.fillText(centerLabel.toUpperCase(), centerX, centerY + 12);
                    }
                    
                    ctx.restore();
                }
            }]
        });
    }

    makeDoughnut(
        'praktikanGenderChart',
        @json($chartPraktikanGenderLabels),
        @json($chartPraktikanGenderData),
        ['#3b82f6', '#ec4899'],
        '{{ $jumlahPraktikan }}',
        'Praktikan'
    );

    makeDoughnut(
        'asistenGenderChart',
        @json($chartAsistenGenderLabels),
        @json($chartAsistenGenderData),
        ['#3b82f6', '#ec4899'],
        '{{ $jumlahAsisten }}',
        'Asisten'
    );

    makeDoughnut(
        'kelompokProdiChart',
        @json($chartKelompokProdiLabels),
        @json($chartKelompokProdiData),
        ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f43f5e', '#64748b'],
        '{{ $totalKelompok }}',
        'Kelompok'
    );

    makeDoughnut(
        'nilaiAkhirChart',
        @json($chartNilaiAkhirLabels),
        @json($chartNilaiAkhirData),
        ['#10b981', '#e2e8f0'],
        '{{ number_format($rataNilaiAkhir, 2) }}',
        'Rata-rata'
    );
</script>
@endsection