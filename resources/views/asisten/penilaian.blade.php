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
            <table class="w-full min-w-[1200px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">NIM</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">Program Studi</th>
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
                            <td class="p-3 text-center">{{ $praktikan->user->prodi ?? '-' }}</td>
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
                            <td colspan="14" class="p-5 text-center text-slate-500">
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

    {{-- Rekap Distribusi Nilai --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">
                        Rekap Distribusi Nilai
                    </h2>

                    <p class="text-slate-500 mt-1">
                        Diagram berikut menampilkan jumlah praktikan berdasarkan nilai huruf.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3">
                        <p class="text-xs text-slate-500 font-bold">Total</p>
                        <p class="text-xl font-extrabold text-slate-900">{{ $totalPraktikanChart ?? 0 }}</p>
                    </div>

                    <div class="rounded-2xl bg-green-50 border border-green-100 px-4 py-3">
                        <p class="text-xs text-green-700 font-bold">Dinilai</p>
                        <p class="text-xl font-extrabold text-slate-900">{{ $totalDinilai ?? 0 }}</p>
                    </div>

                    <div class="rounded-2xl bg-amber-50 border border-amber-100 px-4 py-3">
                        <p class="text-xs text-amber-700 font-bold">Belum Dinilai</p>
                        <p class="text-xl font-extrabold text-slate-900">{{ $totalBelumDinilai ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 xl:grid-cols-12 gap-6">
            <div class="xl:col-span-5 rounded-3xl bg-slate-50 border border-slate-100 p-6">
                @if (($totalDinilai ?? 0) > 0)
                    <div class="h-80">
                        <canvas id="nilaiHurufChart"></canvas>
                    </div>
                @else
                    <div class="h-80 flex items-center justify-center text-center">
                        <div>
                            <div class="text-5xl mb-3">📊</div>
                            <h3 class="text-xl font-extrabold text-slate-900">
                                Belum ada nilai akhir
                            </h3>
                            <p class="text-slate-500 mt-2">
                                Diagram akan muncul setelah praktikan memiliki nilai huruf.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="xl:col-span-7 space-y-4">
                @foreach (($rekapNilaiHuruf ?? collect()) as $huruf => $data)
                    <details class="group rounded-3xl border border-blue-100 bg-white overflow-hidden">
                        <summary class="cursor-pointer list-none p-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center font-extrabold text-blue-700">
                                    {{ $huruf }}
                                </div>

                                <div>
                                    <h3 class="font-extrabold text-slate-900">
                                        Nilai {{ $huruf }}
                                    </h3>
                                    <p class="text-sm text-slate-500">
                                        {{ $data['total'] ?? 0 }} praktikan
                                    </p>
                                </div>
                            </div>

                            <span class="text-sm font-bold text-blue-700 group-open:rotate-180 transition">
                                ▼
                            </span>
                        </summary>

                        <div class="border-t border-blue-100 p-5">
                            @if (($data['total'] ?? 0) > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full min-w-[650px] text-sm">
                                        <thead>
                                            <tr class="bg-slate-100">
                                                <th class="p-3 text-center rounded-l-xl">NIM</th>
                                                <th class="p-3 text-center">Nama</th>
                                                <th class="p-3 text-center">Prodi</th>
                                                <th class="p-3 text-center">Kelompok</th>
                                                <th class="p-3 text-center rounded-r-xl">Nilai Angka</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-slate-100">
                                            @foreach ($data['praktikans'] as $item)
                                                <tr>
                                                    <td class="p-3 text-center">{{ $item['nim'] }}</td>
                                                    <td class="p-3 text-center font-semibold">{{ $item['nama'] }}</td>
                                                    <td class="p-3 text-center">{{ $item['prodi'] }}</td>
                                                    <td class="p-3 text-center">{{ $item['kelompok'] }}</td>
                                                    <td class="p-3 text-center font-extrabold">
                                                        {{ $item['nilai_angka'] !== null ? number_format((float) $item['nilai_angka'], 2) : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5 text-center text-slate-500">
                                    Belum ada praktikan yang mendapat nilai {{ $huruf }}.
                                </div>
                            @endif
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

</div>

{{-- script Chart --}}
@php
    $chartLabels = $chartNilaiLabels ?? ['A', 'B', 'C', 'D', 'E'];
    $chartData = $chartNilaiData ?? [0, 0, 0, 0, 0];
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const nilaiHurufCanvas = document.getElementById('nilaiHurufChart');

    if (nilaiHurufCanvas) {
        const labels = {!! json_encode($chartLabels) !!};
        const dataNilai = {!! json_encode($chartData) !!};

        const totalNilai = dataNilai.reduce((total, item) => total + Number(item), 0);

        new Chart(nilaiHurufCanvas, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [
                    {
                        data: dataNilai,
                        backgroundColor: [
                            '#16a34a', // A - hijau
                            '#2563eb', // B - biru
                            '#7c3aed', // C - ungu
                            '#f59e0b', // D - kuning oranye
                            '#dc2626', // E - merah
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 12,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 14,
                            boxHeight: 14,
                            padding: 16,
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const value = Number(context.raw);

                                const percentage = totalNilai > 0
                                    ? ((value / totalNilai) * 100).toFixed(1)
                                    : 0;

                                return `Nilai ${context.label}: ${value} praktikan (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection