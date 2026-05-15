@extends('layouts.praktikan', ['title' => 'Beranda Praktikan'])

@section('content')
<div class="space-y-8">

    <!-- Welcome -->
    <section class="bg-gradient-to-br from-blue-900 via-blue-800 to-cyan-700 rounded-3xl p-8 text-white shadow-xl overflow-hidden relative">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>

        <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
            <div class="lg:col-span-2">
                <p class="text-blue-100 font-semibold mb-2">
                    Selamat datang,
                </p>

                <h1 class="text-3xl md:text-4xl font-extrabold mb-4">
                    {{ $praktikan->user->name }}
                </h1>

                <p class="text-blue-100 leading-relaxed max-w-3xl">
                    Di halaman beranda praktikan ini, Anda dapat melihat informasi kelompok,
                    sesi praktikum, QR Code presensi, ringkasan nilai, dan riwayat kehadiran.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                    <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                        <p class="text-blue-100 text-sm">Praktikum</p>
                        <p class="font-extrabold">{{ $praktikan->praktikum->nama ?? '-' }}</p>
                    </div>

                    <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                        <p class="text-blue-100 text-sm">Kelompok</p>
                        <p class="font-extrabold">{{ $praktikan->kelompok->nama_kelompok ?? 'Belum ditentukan' }}</p>
                    </div>

                    <div class="bg-white/10 border border-white/20 rounded-2xl p-4">
                        <p class="text-blue-100 text-sm">Sesi</p>
                        <p class="font-extrabold">{{ $praktikan->kelompok->sesi ?? 'Belum ditentukan' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-2xl font-extrabold text-slate-900">
                            QR Code Presensi
                        </h3>
                        <p class="text-slate-500 text-sm mt-1">
                            Tunjukkan QR Code ini kepada asisten saat presensi praktikum.
                        </p>
                    </div>

                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl">
                        📷
                    </div>
                </div>

                <div class="flex justify-center">
                    <div class="bg-white p-5 rounded-3xl border-4 border-slate-100 shadow-sm">
                        <img src="{{ $qrSvg }}"
                            alt="QR Code Presensi Praktikan"
                            class="w-72 h-72 object-contain">
                    </div>
                </div>

                <p class="text-xs text-slate-400 text-center mt-4 break-all">
                    Kode QR: {{ $qrContent }}
                </p>

                <a href="{{ route('praktikan.qr.download') }}"
                class="mt-5 w-full inline-flex justify-center px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Download QR Code
                </a>
            </div>
        </div>
    </section>

    <!-- Info Asisten -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Asisten Kelompok</p>
            <h3 class="text-xl font-extrabold text-slate-900">
                {{ $asisten->user->name ?? 'Belum ditentukan' }}
            </h3>
            <p class="text-slate-600 mt-2">
                {{ $asisten->user->email ?? '-' }}
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Nomor Ponsel Asisten</p>
            <h3 class="text-xl font-extrabold text-slate-900">
                {{ $asisten->user->nomor_ponsel ?? '-' }}
            </h3>
            <p class="text-slate-600 mt-2">
                Hubungi asisten sesuai ketentuan praktikum.
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm text-slate-500 mb-1">Jumlah Modul</p>
            <h3 class="text-xl font-extrabold text-slate-900">
                {{ $moduls->count() }} Modul
            </h3>
            <p class="text-slate-600 mt-2">
                Modul mengikuti praktikum yang dipilih.
            </p>
        </div>
    </section>

    <!-- Nilai dan Kehadiran -->
    <section class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <!-- Nilai -->
        <div class="xl:col-span-4 bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-blue-100 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900">Nilai</h3>
                    <p class="text-slate-500 text-sm">Rekap nilai praktikan.</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl">
                    📊
                </div>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-3 text-slate-500">Kehadiran</td>
                            <td class="py-3 font-bold text-right">{{ $nilaiAkhir->kehadiran ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-slate-500">Laporan</td>
                            <td class="py-3 font-bold text-right">{{ $nilaiAkhir->laporan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-slate-500">Responsi</td>
                            <td class="py-3 font-bold text-right">{{ $nilaiAkhir->responsi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-slate-500">Asistensi</td>
                            <td class="py-3 font-bold text-right">{{ $nilaiAkhir->asistensi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-slate-500">Kemampuan Praktik</td>
                            <td class="py-3 font-bold text-right">{{ $nilaiAkhir->kemampuan_praktik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="py-4 text-slate-900 font-extrabold">Nilai Akhir</td>
                            <td class="py-4 text-right">
                                <span class="px-4 py-2 rounded-xl bg-blue-700 text-white font-extrabold">
                                    {{ $nilaiAkhir->nilai_angka ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                @if (!$nilaiAkhir)
                    <div class="mt-4 p-4 rounded-2xl bg-slate-50 text-slate-500 text-center">
                        Nilai belum tersedia.
                    </div>
                @endif
            </div>
        </div>

        <!-- Kehadiran -->
        <div class="xl:col-span-8 bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-blue-100 flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900">Kehadiran</h3>
                    <p class="text-slate-500 text-sm">Data presensi berdasarkan sesi praktikum.</p>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-700 flex items-center justify-center text-2xl">
                    ✅
                </div>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left  p-3 rounded-l-xl">Pertemuan</th>
                            <th class="text-center  p-3">Modul</th>
                            <th class="text-center  p-3 rounded-r-xl">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($moduls as $modul)
                            @php
                                $nomorPertemuan = $loop->iteration;

                                $statusRaw = data_get($nilaiHarian?->kehadiran, (string) $nomorPertemuan);

                                if (is_numeric($statusRaw)) {
                                    $status = (int) $statusRaw >= 100 ? 'hadir' : 'alpa';
                                } else {
                                    $status = strtolower(trim($statusRaw ?? ''));
                                }

                                $labelStatus = match ($status) {
                                    'hadir' => 'Hadir',
                                    'sakit' => 'Sakit',
                                    'izin' => 'Izin',
                                    'alpa' => 'Alpa',
                                    default => 'Belum ada',
                                };

                                $classStatus = match ($status) {
                                    'hadir' => 'bg-green-100 text-green-700',
                                    'sakit', 'izin', 'alpa' => 'bg-red-100 text-red-700',
                                    default => 'bg-slate-100 text-slate-500',
                                };
                            @endphp

                            <tr>
                                <td class="p-3">
                                    Pertemuan {{ $nomorPertemuan }}
                                </td>

                                <td class="p-3">
                                    {{ $modul->nama_modul }}
                                </td>

                                <td class="p-3 text-right">
                                    <span class="px-3 py-1 rounded-full font-bold {{ $classStatus }}">
                                        {{ $labelStatus }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-4 text-center text-slate-500">
                                    Modul belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection