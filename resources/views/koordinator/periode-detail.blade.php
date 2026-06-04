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

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3">
                Terjadi kesalahan input:
            </h2>

            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">
            Ubah Periode Praktikum
        </h2>

        <form method="POST"
              action="{{ route('koordinator.periode.update', $praktikum) }}"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nama Praktikum
                </label>

                <select name="nama"
                        id="nama_praktikum"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
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
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jenis Praktikum
                </label>

                <select name="jenis_praktikum"
                        id="jenis_praktikum"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
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
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Tahun Akademik
                </label>

                <input type="text"
                       name="tahun_akademik"
                       value="{{ old('tahun_akademik', $praktikum->tahun_akademik) }}"
                       required
                       pattern="[0-9]{4}/[0-9]{4}"
                       placeholder="2025/2026"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-slate-400 mt-2">
                    Gunakan format tahun/tahun, contoh: 2025/2026.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Semester
                </label>

                <select name="semester"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
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
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Jumlah Program Studi</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $jumlahProgramStudi }}
            </h3>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Jumlah Praktikan</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $jumlahPraktikan }}
            </h3>

            <p class="text-xs text-slate-500 mt-2">
                Laki-laki: {{ $praktikanLaki }} · Perempuan: {{ $praktikanPerempuan }}
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Jumlah Asisten</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $jumlahAsisten }}
            </h3>

            <p class="text-xs text-slate-500 mt-2">
                Laki-laki: {{ $asistenLaki }} · Perempuan: {{ $asistenPerempuan }}
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Total Kelompok</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $totalKelompok }}
            </h3>

            <p class="text-xs text-slate-500 mt-2">
                Gabungan dari semua program studi.
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500">Pertemuan Berjalan</p>
            <h3 class="text-4xl font-extrabold text-slate-900 mt-2">
                {{ $pertemuanBerjalan }} / {{ $totalPertemuan }}
            </h3>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Total Kelompok per Program Studi
            </h2>

            <p class="text-slate-500 mt-1">
                Jumlah kelompok dihitung berdasarkan kelompok praktikan pada setiap program studi.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[600px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">Program Studi</th>
                        <th class="p-3 text-center rounded-r-xl">Jumlah Kelompok</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($kelompokPerProdi as $prodi => $jumlah)
                        <tr>
                            <td class="p-3 text-center font-bold">
                                {{ $prodi }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $jumlah }}
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
                    <tr class="bg-blue-50">
                        <td class="p-3 text-center font-extrabold text-blue-900">
                            Total Kelompok
                        </td>

                        <td class="p-3 text-center font-extrabold text-blue-900">
                            {{ $totalKelompok }}
                        </td>
                    </tr>
                </tfoot>
            </table>
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
                <p class="text-4xl font-extrabold text-slate-900">
                    {{ number_format($rataNilaiAkhir, 2) }} / 100
                </p>
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
                cutout: '60%',
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
        @json($chartPraktikanGenderLabels),
        @json($chartPraktikanGenderData),
        ['#2563eb', '#ec4899']
    );

    makeDoughnut(
        'asistenGenderChart',
        @json($chartAsistenGenderLabels),
        @json($chartAsistenGenderData),
        ['#0f766e', '#f97316']
    );

    makeDoughnut(
        'kelompokProdiChart',
        @json($chartKelompokProdiLabels),
        @json($chartKelompokProdiData),
        ['#7c3aed', '#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#0891b2', '#be123c', '#4338ca']
    );

    makeDoughnut(
        'nilaiAkhirChart',
        @json($chartNilaiAkhirLabels),
        @json($chartNilaiAkhirData),
        ['#16a34a', '#e5e7eb']
    );
</script>
@endsection