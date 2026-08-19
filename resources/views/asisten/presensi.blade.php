@extends('layouts.asisten', ['title' => 'Presensi Praktikan'])

@section('content')
<div class="space-y-8 w-full max-w-full">

    {{-- Header Section --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    Presensi Praktikan
                </h1>

                <p class="text-slate-500 mt-2 max-w-2xl leading-relaxed">
                    Kelola dan pantau presensi kehadiran praktikan melalui pemindaian kamera QR Code atau pengisian manual.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <button type="button"
                        id="btn-open-scanner"
                        class="px-5 py-3.5 rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-700 text-white font-extrabold shadow-md hover:shadow-lg hover:from-blue-800 hover:to-indigo-800 transition flex items-center gap-2.5 cursor-pointer">
                    <span class="text-lg">📷</span>
                    <span>Scan QR Praktikan</span>
                </button>
            </div>
        </div>
    </section>

    {{-- Status Alert --}}
    @if (session('status'))
        <section class="bg-emerald-50 border border-emerald-200 rounded-3xl p-5 text-emerald-800 font-bold flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 text-base font-black">
                ✓
            </div>
            <div>
                {{ session('status') }}
            </div>
        </section>
    @endif

    {{-- Filter Praktikan --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-lg font-black text-slate-900 mb-4 flex items-center gap-2">
            <span>🔍</span>
            <span>Filter Data Praktikan</span>
        </h2>

        <form method="GET" action="{{ route('asisten.presensi') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Cari Praktikan</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="NIM atau nama praktikan..."
                       class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 placeholder:text-slate-400">
                <p class="text-[11px] text-slate-400 mt-1.5">
                    Pencarian Sequential Search
                </p>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Kelompok</label>
                <select name="kelompok"
                        class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kelompok</option>
                    @foreach ($kelompoks as $kelompok)
                        <option value="{{ $kelompok->nama_kelompok }}"
                                @selected(request('kelompok') === $kelompok->nama_kelompok)>
                            {{ $kelompok->nama_kelompok }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Program Studi</label>
                <select name="prodi"
                        class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Prodi</option>
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected(request('prodi') == $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Sesi</label>
                <select name="sesi"
                        class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Sesi</option>
                    @foreach ($sesis as $sesi)
                        <option value="{{ $sesi }}" @selected(request('sesi') == $sesi)>
                            {{ $sesi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 lg:col-span-4 flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('asisten.presensi') }}"
                   class="px-5 py-2.5 rounded-2xl bg-slate-100 text-slate-700 text-sm font-extrabold hover:bg-slate-200 transition cursor-pointer">
                    Reset Filter
                </a>

                <button type="submit"
                        class="px-6 py-2.5 rounded-2xl bg-blue-700 text-white text-sm font-extrabold hover:bg-blue-800 transition shadow-sm cursor-pointer">
                    Terapkan Filter
                </button>
            </div>
        </form>

        {{-- Scanner Panel (Camera & Result Form) --}}
        <div id="scanner-panel"
             class="hidden mt-6 rounded-3xl border border-blue-200 bg-blue-50/40 p-6">

            <div class="flex flex-col lg:flex-row gap-6 max-w-full">
                {{-- Camera Viewfinder --}}
                <div class="lg:w-1/2 min-w-0">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <span>📷</span>
                            <span>Kamera Pemindai QR</span>
                        </h3>

                        <div class="flex items-center gap-2">
                            <button type="button"
                                    id="btn-switch-camera"
                                    class="hidden px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-extrabold hover:bg-slate-200 transition cursor-pointer"
                                    title="Ganti Kamera">
                                🔄 Ganti Kamera
                            </button>

                            <button type="button"
                                    id="btn-stop-scanner"
                                    class="px-3.5 py-1.5 rounded-xl bg-red-600 text-white text-xs font-extrabold hover:bg-red-700 transition cursor-pointer">
                                Tutup Kamera
                            </button>
                        </div>
                    </div>

                    {{-- Camera Selection (if multiple) --}}
                    <div id="camera-select-container" class="hidden mb-3">
                        <select id="camera-select" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 bg-white font-medium">
                        </select>
                    </div>

                    <div id="qr-reader"
                         class="w-full overflow-hidden rounded-2xl bg-black border border-blue-100 shadow-xs min-h-[260px] flex items-center justify-center">
                    </div>

                    <p id="scan-message" class="mt-3 text-xs font-bold text-slate-600">
                        Arahkan kamera ke QR Code praktikan.
                    </p>

                    {{-- Alternative: Upload image or search manually --}}
                    <div class="mt-4 pt-4 border-t border-blue-100/80 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label for="qr-file-input"
                                   class="w-full px-3 py-2 rounded-xl bg-white border border-blue-200 hover:bg-blue-50 text-slate-700 text-xs font-bold flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs transition">
                                <span>🖼️</span>
                                <span>Upload Foto QR</span>
                            </label>
                            <input type="file" id="qr-file-input" accept="image/*" class="hidden">
                        </div>

                        <div class="flex gap-1.5">
                            <input type="text"
                                   id="manual-qr-input"
                                   placeholder="NIM praktikan..."
                                   class="w-full px-3 py-2 text-xs rounded-xl border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500">
                            <button type="button"
                                    id="btn-manual-search"
                                    class="px-3 py-2 rounded-xl bg-blue-700 text-white text-xs font-extrabold hover:bg-blue-800 transition shrink-0">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Result Form --}}
                <div class="lg:w-1/2 min-w-0">
                    <form method="POST"
                          action="{{ route('asisten.presensi.scan.store') }}"
                          id="scan-form"
                          class="hidden bg-white rounded-2xl border border-blue-100 p-5 shadow-xs space-y-4">
                        @csrf

                        <input type="hidden" name="qr_code" id="scan_qr_code">

                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                <span>🎯</span>
                                <span>Data Hasil Scan</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">
                                    Teridentifikasi
                                </span>
                                <button type="button"
                                        id="btn-rescan"
                                        class="text-xs font-bold text-blue-700 hover:text-blue-800 underline cursor-pointer">
                                    Scan Ulang
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 text-xs">
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-500 uppercase">Timestamp</p>
                                <p class="font-bold text-slate-900 mt-0.5" id="scan_timestamp">-</p>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-500 uppercase">NIM</p>
                                <p class="font-bold text-slate-900 mt-0.5" id="scan_nim">-</p>
                            </div>
                            <div class="col-span-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-500 uppercase">Nama Praktikan</p>
                                <p class="font-bold text-slate-900 mt-0.5 text-sm" id="scan_nama">-</p>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-500 uppercase">Kelompok</p>
                                <p class="font-bold text-blue-700 mt-0.5" id="scan_kelompok">-</p>
                            </div>
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-500 uppercase">Sesi</p>
                                <p class="font-bold text-slate-900 mt-0.5" id="scan_sesi">-</p>
                            </div>
                            <div class="col-span-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-500 uppercase">Email</p>
                                <p class="font-medium text-slate-700 mt-0.5 break-all" id="scan_email_asisten">-</p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Pertemuan / Modul
                                </label>

                                <select name="modul_id"
                                        required
                                        class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Pertemuan</option>
                                    @foreach ($moduls as $modul)
                                        <option value="{{ $modul->id }}">
                                            Pertemuan {{ $loop->iteration }} - {{ $modul->nama_modul }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Status Kehadiran
                                </label>

                                <select name="status"
                                        required
                                        class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Status</option>
                                    <option value="hadir" selected>Hadir</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="izin">Izin</option>
                                    <option value="alpa">Alpa</option>
                                </select>
                            </div>

                            <button type="submit"
                                    class="w-full px-5 py-3 rounded-xl bg-blue-700 text-white text-xs font-extrabold hover:bg-blue-800 transition shadow-sm cursor-pointer">
                                Simpan Presensi Scan
                            </button>
                        </div>
                    </form>

                    <div id="scan-empty"
                         class="bg-white rounded-2xl border border-dashed border-blue-200 p-8 text-center text-slate-500 flex flex-col items-center justify-center min-h-[260px]">
                        <span class="text-3xl mb-2">📱</span>
                        <p class="text-sm font-extrabold text-slate-700">Menunggu Pemindaian QR</p>
                        <p class="text-xs text-slate-400 mt-1">Data praktikan akan otomatis terisi setelah QR berhasil dipindai atau dicari.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Rekap Presensi Hasil Scan QR --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden w-full">
        <div class="p-6 border-b border-blue-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Rekap Presensi Hasil Scan QR
                </h2>

                <p class="text-slate-500 text-sm mt-1">
                    Riwayat 10 presensi praktikan terakhir yang dicatat melalui pemindaian QR Code.
                </p>
            </div>

            <span class="text-xs font-bold px-3.5 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100 self-start sm:self-auto">
                Total: {{ ($rekapScanPresensis ?? collect())->count() }} Riwayat
            </span>
        </div>

        <div class="p-6">
            {{-- Desktop View Table --}}
            <div class="hidden md:block rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-extrabold uppercase tracking-wider">
                            <th class="p-3.5 text-center w-36">Waktu Scan</th>
                            <th class="p-3.5 text-left">Praktikan</th>
                            <th class="p-3.5 text-center w-36">Kelompok / Sesi</th>
                            <th class="p-3.5 text-center">Modul Praktikum</th>
                            <th class="p-3.5 text-center w-28">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse (($rekapScanPresensis ?? collect()) as $presensi)
                            @php
                                $indexPertemuan = $moduls->pluck('id')->search($presensi->modul_id);
                                $nomorPertemuan = $indexPertemuan !== false ? $indexPertemuan + 1 : null;

                                $status = strtolower($presensi->status ?? '');

                                $statusClass = match ($status) {
                                    'hadir' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'sakit' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'izin' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'alpa' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                            @endphp

                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="p-3.5 text-center text-slate-600 font-medium">
                                    {{ $presensi->waktu_absen
                                        ? $presensi->waktu_absen->format('d/m/Y H:i')
                                        : '-' }}
                                </td>

                                <td class="p-3.5 text-left">
                                    <div class="font-extrabold text-slate-900">{{ $presensi->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 font-medium">{{ $presensi->nim ?? '-' }}</div>
                                </td>

                                <td class="p-3.5 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 font-bold text-xs">
                                        {{ $presensi->kelompok ?? '-' }} · {{ $presensi->sesi ?? '-' }}
                                    </span>
                                </td>

                                <td class="p-3.5 text-center">
                                    @if ($nomorPertemuan)
                                        <span class="font-extrabold text-slate-900">Pertemuan {{ $nomorPertemuan }}</span>
                                        <span class="text-slate-400 mx-1">·</span>
                                        <span class="text-slate-600">{{ $presensi->modul->nama_modul ?? '-' }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="p-3.5 text-center">
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-black border {{ $statusClass }}">
                                        {{ $presensi->status ? ucfirst($presensi->status) : 'Kosong' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">
                                    <span class="text-2xl block mb-1">📋</span>
                                    Belum ada riwayat presensi dari hasil scan QR.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden space-y-3">
                @forelse (($rekapScanPresensis ?? collect()) as $presensi)
                    @php
                        $indexPertemuan = $moduls->pluck('id')->search($presensi->modul_id);
                        $nomorPertemuan = $indexPertemuan !== false ? $indexPertemuan + 1 : null;

                        $status = strtolower($presensi->status ?? '');

                        $statusClass = match ($status) {
                            'hadir' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'sakit' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'izin' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'alpa' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                        };
                    @endphp

                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs space-y-2.5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-sm">
                                    {{ $presensi->nama ?? '-' }}
                                </h3>
                                <p class="text-xs text-slate-500 font-medium">
                                    {{ $presensi->nim ?? '-' }} · {{ $presensi->kelompok ?? '-' }}
                                </p>
                            </div>

                            <span class="shrink-0 px-2.5 py-0.5 rounded-full text-xs font-black border {{ $statusClass }}">
                                {{ $presensi->status ? ucfirst($presensi->status) : 'Kosong' }}
                            </span>
                        </div>

                        <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center justify-between">
                            <span>{{ $nomorPertemuan ? 'P' . $nomorPertemuan . ': ' . ($presensi->modul->nama_modul ?? '-') : '-' }}</span>
                            <span class="text-slate-400 text-[11px]">{{ $presensi->waktu_absen ? $presensi->waktu_absen->format('d/m H:i') : '' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 p-6 text-center text-slate-400 text-sm">
                        <span class="text-2xl block mb-1">📋</span>
                        Belum ada riwayat presensi scan QR.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Data Presensi Manual (Form dengan Tombol Simpan) --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden w-full">
        <form method="POST" action="{{ route('asisten.presensi.update') }}">
            @csrf

            <div class="p-6 border-b border-blue-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                        Data Presensi Manual
                    </h2>

                    <p class="text-slate-500 text-sm mt-1">
                        Ubah status presensi praktikan lalu tekan tombol <strong>Simpan Perubahan</strong> untuk menyimpan.
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-extrabold text-slate-500 mr-1">Petunjuk:</span>
                    <span class="px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-black">H = Hadir</span>
                    <span class="px-2 py-0.5 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-black">S = Sakit</span>
                    <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-[11px] font-black">I = Izin</span>
                    <span class="px-2 py-0.5 rounded-lg bg-rose-50 text-rose-800 border border-rose-200 text-[11px] font-black">A = Alpa</span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Desktop Responsive Table (Fits 100% without horizontal scroll) --}}
                <div class="hidden lg:block rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-extrabold uppercase tracking-wider text-[11px]">
                                <th class="p-3 text-left">Praktikan</th>
                                <th class="p-3 text-center w-28">Kelompok</th>

                                @foreach ($moduls as $modul)
                                    <th class="p-2 text-center w-12" title="{{ $modul->nama_modul }}">
                                        P{{ $loop->iteration }}
                                    </th>
                                @endforeach

                                <th class="p-3 text-center w-20">
                                    Nilai
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($praktikans as $praktikan)
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="p-3 text-left">
                                        <div class="font-extrabold text-slate-900">{{ $praktikan->user->name ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-500 font-medium">
                                            {{ $praktikan->user->nim ?? '-' }} · <span class="text-slate-400">{{ $praktikan->user->prodi ?? '-' }}</span>
                                        </div>
                                    </td>

                                    <td class="p-3 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 font-bold text-xs">
                                            {{ $praktikan->kelompok->nama_kelompok ?? '-' }}
                                        </span>
                                    </td>

                                    @foreach ($moduls as $modul)
                                        @php
                                            $status = data_get(
                                                $praktikan->nilaiHarian?->kehadiran,
                                                (string) $modul->id
                                            );

                                            $selectColorClass = match ($status) {
                                                'hadir' => 'bg-emerald-50 border-emerald-300 text-emerald-800 font-black',
                                                'sakit' => 'bg-amber-50 border-amber-300 text-amber-800 font-black',
                                                'izin' => 'bg-blue-50 border-blue-300 text-blue-800 font-black',
                                                'alpa' => 'bg-rose-50 border-rose-300 text-rose-800 font-black',
                                                default => 'bg-slate-50 border-slate-200 text-slate-400 font-semibold',
                                            };
                                        @endphp

                                        <td class="p-2 text-center">
                                            <select name="presensi[{{ $praktikan->id }}][{{ $modul->id }}]"
                                                    onchange="updateSelectColor(this)"
                                                    title="P{{ $loop->iteration }} - {{ $modul->nama_modul }}"
                                                    class="appearance-none [background-image:none] text-center w-10 h-10 rounded-2xl text-xs font-black transition cursor-pointer hover:scale-105 shadow-2xs focus:ring-2 focus:ring-blue-400 focus:outline-hidden p-0 mx-auto block {{ $selectColorClass }}"
                                                    style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none !important; text-align: center; text-align-last: center; padding: 0 !important;">
                                                <option value="kosong" @selected(!$status)>-</option>
                                                <option value="hadir" @selected($status === 'hadir')>H</option>
                                                <option value="sakit" @selected($status === 'sakit')>S</option>
                                                <option value="izin" @selected($status === 'izin')>I</option>
                                                <option value="alpa" @selected($status === 'alpa')>A</option>
                                            </select>
                                        </td>
                                    @endforeach

                                    <td class="p-3 text-center">
                                        <span class="inline-block px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-black text-xs">
                                            {{ $praktikan->nilaiHarian->rata_rata_kehadiran ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 3 + $moduls->count() }}"
                                        class="p-8 text-center text-slate-400">
                                        <span class="text-2xl block mb-1">👥</span>
                                        Data praktikan belum tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile / Tablet Card View (Strictly Vertical Scroll) --}}
                <div class="lg:hidden space-y-4">
                    @forelse ($praktikans as $praktikan)
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-extrabold text-slate-900">
                                        {{ $praktikan->user->name ?? '-' }}
                                    </h3>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        {{ $praktikan->user->nim ?? '-' }} · {{ $praktikan->user->prodi ?? '-' }}
                                    </p>
                                    <p class="text-xs text-blue-700 font-bold mt-1">
                                        {{ $praktikan->kelompok->nama_kelompok ?? '-' }} (Sesi {{ $praktikan->kelompok->sesi ?? '-' }})
                                    </p>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Nilai Kehadiran</p>
                                    <span class="inline-block px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 font-black text-xs mt-1">
                                        {{ $praktikan->nilaiHarian->rata_rata_kehadiran ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2.5">
                                    Presensi Pertemuan
                                </p>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                    @foreach ($moduls as $modul)
                                        @php
                                            $status = data_get(
                                                $praktikan->nilaiHarian?->kehadiran,
                                                (string) $modul->id
                                            );

                                            $selectColorClass = match ($status) {
                                                'hadir' => 'bg-emerald-50 border-emerald-300 text-emerald-800 font-black',
                                                'sakit' => 'bg-amber-50 border-amber-300 text-amber-800 font-black',
                                                'izin' => 'bg-blue-50 border-blue-300 text-blue-800 font-black',
                                                'alpa' => 'bg-rose-50 border-rose-300 text-rose-800 font-black',
                                                default => 'bg-slate-50 border-slate-200 text-slate-400 font-semibold',
                                            };
                                        @endphp

                                        <div class="rounded-2xl bg-blue-50/50 border border-blue-100 p-2.5 text-center">
                                            <p class="text-[10px] font-black text-blue-700 uppercase">
                                                P{{ $loop->iteration }}
                                            </p>
                                            <p class="text-[9px] text-slate-500 truncate mt-0.5 mb-2" title="{{ $modul->nama_modul }}">
                                                {{ $modul->nama_modul }}
                                            </p>

                                            <select name="presensi[{{ $praktikan->id }}][{{ $modul->id }}]"
                                                    onchange="updateSelectColor(this)"
                                                    class="appearance-none [background-image:none] text-center w-full h-9.5 rounded-xl text-xs font-black transition cursor-pointer shadow-2xs focus:ring-2 focus:ring-blue-400 focus:outline-hidden p-0 mx-auto block {{ $selectColorClass }}"
                                                    style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none !important; text-align: center; text-align-last: center; padding: 0 !important;">
                                                <option value="kosong" @selected(!$status)>-</option>
                                                <option value="hadir" @selected($status === 'hadir')>H</option>
                                                <option value="sakit" @selected($status === 'sakit')>S</option>
                                                <option value="izin" @selected($status === 'izin')>I</option>
                                                <option value="alpa" @selected($status === 'alpa')>A</option>
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center text-slate-400">
                            <span class="text-2xl block mb-1">👥</span>
                            Data praktikan belum tersedia.
                        </div>
                    @endforelse
                </div>

                {{-- Action Bar: Tombol Simpan Perubahan Presensi --}}
                @if ($praktikans->isNotEmpty())
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-xs text-slate-500">
                            💡 Pilih status presensi (Hadir, Sakit, Izin, Alpa) untuk praktikan yang diinginkan, lalu klik tombol simpan.
                        </p>

                        <button type="submit"
                                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-gradient-to-r from-blue-700 to-indigo-700 text-white font-extrabold text-sm shadow-md hover:shadow-lg hover:from-blue-800 hover:to-indigo-800 transition flex items-center justify-center gap-2 cursor-pointer">
                            <span>💾</span>
                            <span>Simpan Perubahan Presensi</span>
                        </button>
                    </div>
                @endif

                @if (method_exists($praktikans, 'links'))
                    <div class="mt-4">
                        {{ $praktikans->links() }}
                    </div>
                @endif
            </div>
        </form>
    </section>
</div>

{{-- Script scanner & color updater --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    // Update select box background color dynamically when changed
    function updateSelectColor(selectEl) {
        const val = selectEl.value.toLowerCase();
        
        selectEl.classList.remove(
            'bg-emerald-50', 'border-emerald-300', 'text-emerald-800',
            'bg-amber-50', 'border-amber-300', 'text-amber-800',
            'bg-blue-50', 'border-blue-300', 'text-blue-800',
            'bg-rose-50', 'border-rose-300', 'text-rose-800',
            'bg-slate-50', 'border-slate-200', 'text-slate-400', 'font-medium', 'font-semibold'
        );

        if (val === 'hadir') {
            selectEl.classList.add('bg-emerald-50', 'border-emerald-300', 'text-emerald-800', 'font-black');
        } else if (val === 'sakit') {
            selectEl.classList.add('bg-amber-50', 'border-amber-300', 'text-amber-800', 'font-black');
        } else if (val === 'izin') {
            selectEl.classList.add('bg-blue-50', 'border-blue-300', 'text-blue-800', 'font-black');
        } else if (val === 'alpa') {
            selectEl.classList.add('bg-rose-50', 'border-rose-300', 'text-rose-800', 'font-black');
        } else {
            selectEl.classList.add('bg-slate-50', 'border-slate-200', 'text-slate-400', 'font-semibold');
        }
    }

    // QR Code Scanner Logic
    const openScannerButton = document.getElementById('btn-open-scanner');
    const stopScannerButton = document.getElementById('btn-stop-scanner');
    const switchCameraButton = document.getElementById('btn-switch-camera');
    const cameraSelectContainer = document.getElementById('camera-select-container');
    const cameraSelect = document.getElementById('camera-select');
    const scannerPanel = document.getElementById('scanner-panel');
    const scanMessage = document.getElementById('scan-message');
    const scanForm = document.getElementById('scan-form');
    const scanEmpty = document.getElementById('scan-empty');
    const rescanButton = document.getElementById('btn-rescan');

    const qrFileInput = document.getElementById('qr-file-input');
    const manualQrInput = document.getElementById('manual-qr-input');
    const btnManualSearch = document.getElementById('btn-manual-search');

    const scanQrCodeInput = document.getElementById('scan_qr_code');
    const scanTimestamp = document.getElementById('scan_timestamp');
    const scanEmailAsisten = document.getElementById('scan_email_asisten');
    const scanNim = document.getElementById('scan_nim');
    const scanNama = document.getElementById('scan_nama');
    const scanKelompok = document.getElementById('scan_kelompok');
    const scanSesi = document.getElementById('scan_sesi');

    let html5QrCode = null;
    let isScanning = false;
    let hasScanned = false;
    let availableCameras = [];
    let currentCameraIndex = 0;

    function playBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(800, ctx.currentTime);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.15);
        } catch (e) {
            // Audio context not allowed or unsupported
        }
    }

    async function initCameras() {
        try {
            availableCameras = await Html5Qrcode.getCameras();
            if (availableCameras && availableCameras.length > 0) {
                cameraSelect.innerHTML = '';
                availableCameras.forEach((cam, idx) => {
                    const opt = document.createElement('option');
                    opt.value = cam.id;
                    opt.textContent = cam.label || `Kamera ${idx + 1}`;
                    cameraSelect.appendChild(opt);
                });

                if (availableCameras.length > 1) {
                    cameraSelectContainer.classList.remove('hidden');
                    switchCameraButton.classList.remove('hidden');
                }
            }
        } catch (err) {
            console.warn('Could not enumerate cameras:', err);
        }
    }

    async function startScanner(preferredCameraId = null) {
        scannerPanel.classList.remove('hidden');
        scanMessage.textContent = 'Memulai kamera...';
        scanMessage.className = 'mt-3 text-xs font-bold text-slate-600';

        scanForm.classList.add('hidden');
        scanEmpty.classList.remove('hidden');

        hasScanned = false;

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('qr-reader');
        }

        if (availableCameras.length === 0) {
            await initCameras();
        }

        const qrboxFunction = function(viewfinderWidth, viewfinderHeight) {
            const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
            const qrboxSize = Math.max(180, Math.floor(minEdge * 0.75));
            return {
                width: qrboxSize,
                height: qrboxSize
            };
        };

        const config = {
            fps: 15,
            qrbox: qrboxFunction,
            aspectRatio: 1.0,
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            }
        };

        try {
            if (isScanning) {
                await html5QrCode.stop();
                isScanning = false;
            }

            let cameraConfig;
            if (preferredCameraId) {
                cameraConfig = { deviceId: { exact: preferredCameraId } };
            } else if (availableCameras.length > 0) {
                // Find back camera if possible
                const backCam = availableCameras.find(c => (c.label || '').toLowerCase().includes('back') || (c.label || '').toLowerCase().includes('belakang') || (c.label || '').toLowerCase().includes('environment'));
                cameraConfig = backCam ? { deviceId: { exact: backCam.id } } : { deviceId: { exact: availableCameras[0].id } };
            } else {
                cameraConfig = { facingMode: 'environment' };
            }

            try {
                await html5QrCode.start(cameraConfig, config, onScanSuccess);
            } catch (firstErr) {
                // Fallback to user / default camera if facingMode environment or exact deviceId failed
                console.warn('First camera start failed, trying generic fallback:', firstErr);
                await html5QrCode.start({ facingMode: 'user' }, config, onScanSuccess);
            }

            isScanning = true;
            scanMessage.textContent = 'Arahkan kamera ke QR Code praktikan.';
            scanMessage.className = 'mt-3 text-xs font-bold text-slate-600';
        } catch (error) {
            console.error('Scanner error:', error);
            scanMessage.textContent = 'Kamera tidak bisa dibuka. Pastikan izin kamera sudah diberikan atau gunakan Upload Foto QR / Cari NIM.';
            scanMessage.className = 'mt-3 text-xs font-bold text-rose-600';
        }
    }

    async function stopScanner() {
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.stop();
            } catch (e) {
                console.warn('Error stopping scanner:', e);
            }
            isScanning = false;
        }

        scanMessage.textContent = 'Kamera dihentikan.';
    }

    async function handleQrText(decodedText) {
        scanMessage.textContent = 'Memverifikasi data praktikan: ' + decodedText + '...';
        scanMessage.className = 'mt-3 text-xs font-bold text-blue-700';

        try {
            const response = await fetch("{{ route('asisten.presensi.scan') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    qr_code: decodedText.trim()
                })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                scanMessage.textContent = result.message ?? 'QR Code tidak valid atau praktikan tidak ditemukan.';
                scanMessage.className = 'mt-3 text-xs font-bold text-rose-600';
                hasScanned = false;
                return false;
            }

            playBeep();

            scanQrCodeInput.value = result.data.qr_code;
            scanTimestamp.textContent = result.data.timestamp;
            scanEmailAsisten.textContent = result.data.email_asisten;
            scanNim.textContent = result.data.nim;
            scanNama.textContent = result.data.nama;
            scanKelompok.textContent = result.data.kelompok;
            scanSesi.textContent = result.data.sesi;

            scanForm.classList.remove('hidden');
            scanEmpty.classList.add('hidden');

            scanMessage.textContent = 'Praktikan ' + result.data.nama + ' (' + result.data.nim + ') berhasil ditemukan!';
            scanMessage.className = 'mt-3 text-xs font-bold text-emerald-700';

            return true;
        } catch (error) {
            console.error('Fetch error:', error);
            scanMessage.textContent = 'Terjadi kesalahan jaringan saat memverifikasi QR Code.';
            scanMessage.className = 'mt-3 text-xs font-bold text-rose-600';
            hasScanned = false;
            return false;
        }
    }

    async function onScanSuccess(decodedText) {
        if (hasScanned) {
            return;
        }

        hasScanned = true;
        await stopScanner();
        await handleQrText(decodedText);
    }

    // Camera switch dropdown change
    if (cameraSelect) {
        cameraSelect.addEventListener('change', function() {
            if (this.value) {
                startScanner(this.value);
            }
        });
    }

    // Switch camera button
    if (switchCameraButton) {
        switchCameraButton.addEventListener('click', function() {
            if (availableCameras.length > 1) {
                currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
                const nextCam = availableCameras[currentCameraIndex];
                if (cameraSelect) {
                    cameraSelect.value = nextCam.id;
                }
                startScanner(nextCam.id);
            }
        });
    }

    // Upload file QR scanner
    if (qrFileInput) {
        qrFileInput.addEventListener('change', async function(e) {
            if (!e.target.files || e.target.files.length === 0) {
                return;
            }

            const file = e.target.files[0];
            scanMessage.textContent = 'Membaca gambar QR...';
            scanMessage.className = 'mt-3 text-xs font-bold text-blue-700';

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode('qr-reader');
            }

            try {
                if (isScanning) {
                    await stopScanner();
                }

                const decodedText = await html5QrCode.scanFile(file, true);
                await handleQrText(decodedText);
            } catch (err) {
                console.error('Image QR scan failed:', err);
                scanMessage.textContent = 'QR Code pada gambar tidak dapat terbaca. Pastikan gambar jelas dan tidak buram.';
                scanMessage.className = 'mt-3 text-xs font-bold text-rose-600';
            } finally {
                qrFileInput.value = '';
            }
        });
    }

    // Manual search by NIM / QR Code
    if (btnManualSearch && manualQrInput) {
        const doManualSearch = async () => {
            const val = manualQrInput.value.trim();
            if (!val) {
                scanMessage.textContent = 'Masukkan NIM atau kode QR terlebih dahulu.';
                scanMessage.className = 'mt-3 text-xs font-bold text-amber-700';
                return;
            }
            if (isScanning) {
                await stopScanner();
            }
            await handleQrText(val);
        };

        btnManualSearch.addEventListener('click', doManualSearch);
        manualQrInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                doManualSearch();
            }
        });
    }

    // Re-scan button
    if (rescanButton) {
        rescanButton.addEventListener('click', function() {
            startScanner();
        });
    }

    openScannerButton.addEventListener('click', () => startScanner());
    stopScannerButton.addEventListener('click', stopScanner);
</script>
@endsection