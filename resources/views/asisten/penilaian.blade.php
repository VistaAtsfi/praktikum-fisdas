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
        <form method="GET" action="{{ route('asisten.penilaian') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Cari Praktikan</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari NIM atau nama praktikan"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Program Studi</label>
                <select name="prodi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Program Studi</option>
                    @foreach (($prodis ?? collect()) as $prodiItem)
                        <option value="{{ $prodiItem }}" @selected(request('prodi') === $prodiItem)>
                            {{ $prodiItem }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Kelompok</label>
                <select name="kelompok"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Kelompok</option>
                    @foreach ($kelompoks->groupBy('prodi') as $prodiName => $kelompokList)
                        <optgroup label="{{ $prodiName ?: 'Umum' }}">
                            @foreach ($kelompokList as $kelompok)
                                <option value="{{ $kelompok->nama_kelompok }}"
                                        @selected(request('kelompok') === $kelompok->nama_kelompok)>
                                    {{ $kelompok->nama_kelompok }} ({{ $kelompok->prodi }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Status Nilai</label>
                <select name="status_nilai"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Semua Status</option>
                    <option value="sudah" @selected(request('status_nilai') === 'sudah')>Sudah Dinilai</option>
                    <option value="belum" @selected(request('status_nilai') === 'belum')>Belum Dinilai</option>
                </select>
            </div>

            <div class="lg:col-span-4 flex gap-3">
                <button type="submit"
                        class="px-5 py-2.5 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition text-sm">
                    Terapkan
                </button>

                <a href="{{ route('asisten.penilaian') }}"
                   class="px-5 py-2.5 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition text-sm">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100 space-y-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">
                    Daftar Nilai Praktikan
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Kelola dan periksa status penilaian praktikan.
                </p>
            </div>

            {{-- Tabs Navigasi Program Studi di Bawah Teks --}}
            @if (($prodis ?? collect())->isNotEmpty())
                <div class="flex items-center gap-2 overflow-x-auto pb-1 max-w-full">
                    <a href="{{ route('asisten.penilaian', array_merge(request()->except('prodi', 'page'), ['prodi' => ''])) }}"
                       class="px-4 py-2 rounded-xl font-bold text-xs md:text-sm transition flex items-center gap-1.5 shrink-0 {{ !request('prodi') ? 'bg-blue-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        <span>🏛️</span>
                        <span>Semua Prodi</span>
                    </a>

                    @foreach ($prodis as $itemProdi)
                        <a href="{{ route('asisten.penilaian', array_merge(request()->except('prodi', 'page'), ['prodi' => $itemProdi])) }}"
                           class="px-4 py-2 rounded-xl font-bold text-xs md:text-sm transition flex items-center gap-1.5 shrink-0 {{ request('prodi') === $itemProdi ? 'bg-blue-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            <span>{{ $itemProdi }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-100/90 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200/60">
                        <th class="py-3.5 px-4 text-left font-bold rounded-l-xl w-56">Praktikan</th>
                        <th class="py-3.5 px-3 text-center font-bold w-36">Kelompok</th>
                        <th class="py-3.5 px-3 text-left font-bold">Rincian Komponen Nilai</th>
                        <th class="py-3.5 px-3 text-center font-bold w-40">Nilai Akhir</th>
                        <th class="py-3.5 px-3 text-center font-bold rounded-r-xl w-32">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($praktikans as $praktikan)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="py-3.5 px-4 align-middle">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $praktikan->user->name ?? '-' }}
                                </div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">
                                    {{ $praktikan->user->nim ?? '-' }}
                                </div>
                                <div class="text-xs text-blue-600 font-medium mt-0.5">
                                    {{ $praktikan->user->prodi ?? '-' }}
                                </div>
                            </td>

                            <td class="py-3.5 px-3 text-center align-middle">
                                <div class="font-bold text-slate-800 text-sm">
                                    {{ $praktikan->kelompok->nama_kelompok ?? '-' }}
                                </div>
                                @if($praktikan->kelompok?->sesi)
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-xs font-semibold">
                                        Sesi: {{ $praktikan->kelompok->sesi }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-3 align-middle">
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-1.5 text-center">
                                    <div class="bg-slate-50 rounded-lg p-1.5 border border-slate-100">
                                        <span class="text-slate-400 text-[10px] block font-bold uppercase">Kehadiran</span>
                                        <span class="font-extrabold text-xs text-slate-800">{{ $praktikan->nilaiAkhir?->kehadiran ?? $praktikan->nilaiHarian?->rata_rata_kehadiran ?? '-' }}</span>
                                    </div>
                                    <div class="bg-slate-50 rounded-lg p-1.5 border border-slate-100">
                                        <span class="text-slate-400 text-[10px] block font-bold uppercase">Laporan</span>
                                        <span class="font-extrabold text-xs text-slate-800">{{ $praktikan->nilaiAkhir?->laporan ?? $praktikan->nilaiHarian?->laporan ?? '-' }}</span>
                                    </div>
                                    <div class="bg-slate-50 rounded-lg p-1.5 border border-slate-100">
                                        <span class="text-slate-400 text-[10px] block font-bold uppercase">Responsi</span>
                                        <span class="font-extrabold text-xs text-slate-800">{{ $praktikan->nilaiAkhir?->responsi ?? $praktikan->nilaiHarian?->rata_rata_responsi ?? '-' }}</span>
                                    </div>
                                    <div class="bg-slate-50 rounded-lg p-1.5 border border-slate-100">
                                        <span class="text-slate-400 text-[10px] block font-bold uppercase">Asistensi</span>
                                        <span class="font-extrabold text-xs text-slate-800">{{ $praktikan->nilaiAkhir?->asistensi ?? $praktikan->nilaiHarian?->rata_rata_asistensi ?? '-' }}</span>
                                    </div>
                                    <div class="bg-slate-50 rounded-lg p-1.5 border border-slate-100">
                                        <span class="text-slate-400 text-[10px] block font-bold uppercase">Praktik</span>
                                        <span class="font-extrabold text-xs text-slate-800">{{ $praktikan->nilaiAkhir?->kemampuan_praktik ?? $praktikan->nilaiHarian?->rata_rata_kemampuan_praktik ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-3 text-center align-middle">
                                <div class="flex items-center justify-center gap-2.5">
                                    <span class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm
                                        @if(($praktikan->nilaiAkhir?->nilai_huruf ?? '') === 'A') bg-emerald-100 text-emerald-800 border border-emerald-200
                                        @elseif(($praktikan->nilaiAkhir?->nilai_huruf ?? '') === 'B') bg-blue-100 text-blue-800 border border-blue-200
                                        @elseif(($praktikan->nilaiAkhir?->nilai_huruf ?? '') === 'C') bg-purple-100 text-purple-800 border border-purple-200
                                        @elseif(($praktikan->nilaiAkhir?->nilai_huruf ?? '') === 'D') bg-amber-100 text-amber-800 border border-amber-200
                                        @elseif(($praktikan->nilaiAkhir?->nilai_huruf ?? '') === 'E') bg-red-100 text-red-800 border border-red-200
                                        @else bg-slate-100 text-slate-400 border border-slate-200 @endif">
                                        {{ $praktikan->nilaiAkhir?->nilai_huruf ?? '-' }}
                                    </span>
                                    <div class="text-left">
                                        <div class="font-black text-slate-900 text-sm">
                                            {{ $praktikan->nilaiAkhir?->nilai_angka !== null && $praktikan->nilaiAkhir?->nilai_angka !== '' ? number_format((float) $praktikan->nilaiAkhir->nilai_angka, 2) : '-' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-medium">
                                            Bobot: <span class="font-bold text-slate-700">{{ $praktikan->nilaiAkhir?->nilai_bobot !== null && $praktikan->nilaiAkhir?->nilai_bobot !== '' ? number_format((float) $praktikan->nilaiAkhir->nilai_bobot, 2) : '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-3 text-center align-middle">
                                <a href="{{ route('asisten.penilaian.show', $praktikan) }}"
                                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs transition shadow-xs whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Beri Nilai</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-4 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="font-medium text-slate-600">Data praktikan belum tersedia.</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter pencarian atau pilih praktikum lain.</p>
                                </div>
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
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-slate-100/90 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200/60">
                                                <th class="py-3 px-3 text-center rounded-l-xl">NIM</th>
                                                <th class="py-3 px-4 text-left">Nama</th>
                                                <th class="py-3 px-3 text-center">Prodi</th>
                                                <th class="py-3 px-3 text-center">Kelompok</th>
                                                <th class="py-3 px-3 text-center rounded-r-xl">Nilai Angka</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-slate-100">
                                            @foreach ($data['praktikans'] as $item)
                                                <tr class="hover:bg-slate-50/70 transition">
                                                    <td class="py-3 px-3 text-center font-mono text-xs text-slate-600">{{ $item['nim'] }}</td>
                                                    <td class="py-3 px-4 text-left font-bold text-slate-900">{{ $item['nama'] }}</td>
                                                    <td class="py-3 px-3 text-center text-xs text-blue-600 font-medium">{{ $item['prodi'] }}</td>
                                                    <td class="py-3 px-3 text-center font-semibold text-slate-700">{{ $item['kelompok'] }}</td>
                                                    <td class="py-3 px-3 text-center font-black text-slate-900">
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