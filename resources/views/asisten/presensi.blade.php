@extends('layouts.asisten', ['title' => 'Presensi Praktikan'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Presensi Praktikan
        </h1>

        <p class="text-slate-500 mt-2">
            Kelola presensi praktikan melalui scan QR atau input manual.
        </p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('asisten.presensi') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Cari Praktikan</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari NIM atau nama praktikan"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-slate-400 mt-2">
                    Pencarian menggunakan Sequential Search.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Scan QR</label>
                <button type="button"
                        id="btn-open-scanner"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-left font-semibold text-slate-600 hover:border-blue-300 hover:bg-blue-50 transition">
                    📷 Scan QR Praktikan
                </button>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Saring Berdasarkan Kelompok</label>
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
                <label class="block text-sm font-bold text-slate-700 mb-2">Saring Berdasarkan Prodi</label>
                <select name="prodi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Prodi</option>
                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected(request('prodi') == $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Saring Berdasarkan Sesi</label>
                <select name="sesi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Sesi</option>
                    @foreach ($sesis as $sesi)
                        <option value="{{ $sesi }}" @selected(request('sesi') == $sesi)>
                            {{ $sesi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan Filter
                </button>

                <a href="{{ route('asisten.presensi') }}"
                   class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>

        <div id="scanner-panel"
             class="hidden mt-6 rounded-3xl border border-blue-100 bg-blue-50/40 p-5">

            <div class="flex flex-col lg:flex-row gap-6">
                <div class="lg:w-1/2">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-extrabold text-slate-900">
                            Kamera Scan QR
                        </h3>

                        <button type="button"
                                id="btn-stop-scanner"
                                class="px-4 py-2 rounded-xl bg-red-600 text-white text-sm font-bold hover:bg-red-700 transition">
                            Berhenti
                        </button>
                    </div>

                    <div id="qr-reader"
                         class="w-full overflow-hidden rounded-2xl bg-white border border-blue-100">
                    </div>

                    <p id="scan-message" class="mt-3 text-sm font-semibold text-slate-600">
                        Arahkan kamera ke QR Code praktikan.
                    </p>
                </div>

                <div class="lg:w-1/2">
                    <form method="POST"
                          action="{{ route('asisten.presensi.scan.store') }}"
                          id="scan-form"
                          class="hidden bg-white rounded-2xl border border-blue-100 p-5">
                        @csrf

                        <input type="hidden" name="qr_code" id="scan_qr_code">

                        <h3 class="text-xl font-extrabold text-slate-900 mb-4">
                            Data Hasil Scan
                        </h3>

                        <div class="overflow-x-auto mb-5">
                            <table class="w-full text-sm border border-slate-200">
                                <tbody>
                                    <tr>
                                        <td class="border border-slate-200 p-2 font-bold bg-slate-50">Timestamp</td>
                                        <td class="border border-slate-200 p-2" id="scan_timestamp">-</td>
                                    </tr>

                                    <tr>
                                        <td class="border border-slate-200 p-2 font-bold bg-slate-50">Email Address</td>
                                        <td class="border border-slate-200 p-2" id="scan_email_asisten">-</td>
                                    </tr>

                                    <tr>
                                        <td class="border border-slate-200 p-2 font-bold bg-slate-50">NIM</td>
                                        <td class="border border-slate-200 p-2" id="scan_nim">-</td>
                                    </tr>

                                    <tr>
                                        <td class="border border-slate-200 p-2 font-bold bg-slate-50">Nama</td>
                                        <td class="border border-slate-200 p-2" id="scan_nama">-</td>
                                    </tr>

                                    <tr>
                                        <td class="border border-slate-200 p-2 font-bold bg-slate-50">Kelompok</td>
                                        <td class="border border-slate-200 p-2" id="scan_kelompok">-</td>
                                    </tr>

                                    <tr>
                                        <td class="border border-slate-200 p-2 font-bold bg-slate-50">Sesi Praktikum</td>
                                        <td class="border border-slate-200 p-2" id="scan_sesi">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Pertemuan / Modul
                                </label>

                                <select name="modul_id"
                                        required
                                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Pertemuan</option>

                                    @foreach ($moduls as $modul)
                                        <option value="{{ $modul->id }}">
                                            Pertemuan {{ $loop->iteration }} - {{ $modul->nama_modul }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Status Kehadiran
                                </label>

                                <select name="status"
                                        required
                                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Status</option>
                                    <option value="hadir">Hadir</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="izin">Izin</option>
                                    <option value="alpa">Alpa</option>
                                </select>
                            </div>

                            <button type="submit"
                                    class="w-full px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                                Simpan Presensi
                            </button>
                        </div>
                    </form>

                    <div id="scan-empty"
                         class="bg-white rounded-2xl border border-blue-100 p-6 text-center text-slate-500">
                        Data praktikan akan muncul setelah QR berhasil discan.
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Rekap Presensi Hasil Scan QR --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Rekap Presensi Hasil Scan QR
            </h2>

            <p class="text-slate-500 mt-1">
                Data berikut akan muncul setiap kali asisten menyimpan presensi melalui scan QR.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1000px] text-sm border border-slate-200">
                <thead>
                    <tr class="bg-purple-700 text-white">
                        <th class="border border-purple-800 p-3 text-center font-bold">Timestamp</th>
                        <th class="border border-purple-800 p-3 text-center font-bold">Email Address</th>
                        <th class="border border-purple-800 p-3 text-center font-bold">NIM</th>
                        <th class="border border-purple-800 p-3 text-center font-bold">Nama</th>
                        <th class="border border-purple-800 p-3 text-center font-bold">Kelompok</th>
                        <th class="border border-purple-800 p-3 text-center font-bold">Sesi Praktikum</th>
                        <th class="border border-purple-800 p-3 text-center font-bold">Pertemuan</th>
                        <th class="border border-purple-800 p-3 text-center font-bold">Status Kehadiran</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (($rekapScanPresensis ?? collect()) as $presensi)
                        @php
                            $indexPertemuan = $moduls->pluck('id')->search($presensi->modul_id);
                            $nomorPertemuan = $indexPertemuan !== false ? $indexPertemuan + 1 : null;

                            $status = strtolower($presensi->status ?? '');

                            $statusClass = match ($status) {
                                'hadir' => 'bg-green-100 text-green-700',
                                'sakit', 'izin', 'alpa' => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-500',
                            };
                        @endphp

                        <tr>
                            <td class="border border-slate-200 p-3 text-center whitespace-nowrap">
                                {{ $presensi->waktu_absen ? $presensi->waktu_absen->format('n/j/Y H:i:s') : '-' }}
                            </td>

                            <td class="border border-slate-200 p-3 text-center">
                                {{ $presensi->email ?? '-' }}
                            </td>

                            <td class="border border-slate-200 p-3 text-center">
                                {{ $presensi->nim ?? '-' }}
                            </td>

                            <td class="border border-slate-200 p-3 text-center">
                                {{ $presensi->nama ?? '-' }}
                            </td>

                            <td class="border border-slate-200 p-3 text-center">
                                {{ $presensi->kelompok ?? '-' }}
                            </td>

                            <td class="border border-slate-200 p-3 text-center">
                                {{ $presensi->sesi ?? '-' }}
                            </td>

                            <td class="border border-slate-200 p-3 text-center">
                                @if ($nomorPertemuan)
                                    <div class="font-bold text-slate-800">
                                        Pertemuan {{ $nomorPertemuan }}
                                    </div>

                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $presensi->modul->nama_modul ?? '-' }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="border border-slate-200 p-3 text-center">
                                <span class="px-3 py-1 rounded-full font-bold {{ $statusClass }}">
                                    {{ $presensi->status ? ucfirst($presensi->status) : '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-slate-200 p-5 text-center text-slate-500">
                                Belum ada presensi dari hasil scan QR.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Data Presensi Manual --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Data Presensi Manual
            </h2>

            <p class="text-slate-500 mt-1">
                Presensi tetap bisa diubah secara manual melalui tabel ini.
            </p>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">NIM</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">Prodi</th>
                        <th class="p-3 text-center">Kelompok</th>
                        <th class="p-3 text-center">Sesi</th>

                        @foreach ($moduls as $modul)
                            <th class="p-3 text-center whitespace-nowrap">
                                Pertemuan {{ $loop->iteration }}
                            </th>
                        @endforeach

                        <th class="p-3 text-center rounded-r-xl">Nilai Kehadiran</th>
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

                            @foreach ($moduls as $modul)
                                @php
                                    $status = data_get($praktikan->nilaiHarian?->kehadiran, (string) $modul->id);
                                @endphp

                                <td class="p-3 text-center">
                                    <form method="POST" action="{{ route('asisten.presensi.update') }}">
                                        @csrf

                                        <input type="hidden" name="praktikan_id" value="{{ $praktikan->id }}">
                                        <input type="hidden" name="modul_id" value="{{ $modul->id }}">

                                        <select name="status"
                                                onchange="this.form.submit()"
                                                class="rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="kosong" @selected(!$status)>-</option>
                                            <option value="hadir" @selected($status === 'hadir')>Hadir</option>
                                            <option value="sakit" @selected($status === 'sakit')>Sakit</option>
                                            <option value="izin" @selected($status === 'izin')>Izin</option>
                                            <option value="alpa" @selected($status === 'alpa')>Alpa</option>
                                        </select>
                                    </form>
                                </td>
                            @endforeach

                            <td class="p-3 text-center font-bold">
                                {{ $praktikan->nilaiHarian->rata_rata_kehadiran ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 6 + $moduls->count() }}" class="p-5 text-center text-slate-500">
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

</div>

{{-- Script scanner --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    const openScannerButton = document.getElementById('btn-open-scanner');
    const stopScannerButton = document.getElementById('btn-stop-scanner');
    const scannerPanel = document.getElementById('scanner-panel');
    const scanMessage = document.getElementById('scan-message');
    const scanForm = document.getElementById('scan-form');
    const scanEmpty = document.getElementById('scan-empty');

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

    async function startScanner() {
        scannerPanel.classList.remove('hidden');
        scanMessage.textContent = 'Memulai kamera...';
        scanMessage.className = 'mt-3 text-sm font-semibold text-slate-600';

        scanForm.classList.add('hidden');
        scanEmpty.classList.remove('hidden');

        hasScanned = false;

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('qr-reader');
        }

        try {
            await html5QrCode.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                onScanSuccess
            );

            isScanning = true;
            scanMessage.textContent = 'Arahkan kamera ke QR Code praktikan.';
        } catch (error) {
            scanMessage.textContent = 'Kamera tidak bisa dibuka. Pastikan izin kamera sudah diberikan.';
            scanMessage.className = 'mt-3 text-sm font-semibold text-red-600';
        }
    }

    async function stopScanner() {
        if (html5QrCode && isScanning) {
            await html5QrCode.stop();
            isScanning = false;
        }

        scanMessage.textContent = 'Kamera dihentikan.';
    }

    async function onScanSuccess(decodedText) {
        if (hasScanned) {
            return;
        }

        hasScanned = true;
        scanMessage.textContent = 'QR terbaca. Mencari data praktikan...';

        try {
            await stopScanner();

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
                scanMessage.textContent = result.message ?? 'QR Code tidak valid.';
                scanMessage.className = 'mt-3 text-sm font-semibold text-red-600';
                hasScanned = false;
                return;
            }

            scanQrCodeInput.value = result.data.qr_code;
            scanTimestamp.textContent = result.data.timestamp;
            scanEmailAsisten.textContent = result.data.email_asisten;
            scanNim.textContent = result.data.nim;
            scanNama.textContent = result.data.nama;
            scanKelompok.textContent = result.data.kelompok;
            scanSesi.textContent = result.data.sesi;

            scanForm.classList.remove('hidden');
            scanEmpty.classList.add('hidden');

            scanMessage.textContent = 'Data praktikan berhasil ditemukan. Pilih status kehadiran lalu simpan.';
            scanMessage.className = 'mt-3 text-sm font-semibold text-green-700';
        } catch (error) {
            scanMessage.textContent = 'Terjadi kesalahan saat memproses QR Code.';
            scanMessage.className = 'mt-3 text-sm font-semibold text-red-600';
            hasScanned = false;
        }
    }

    openScannerButton.addEventListener('click', startScanner);
    stopScannerButton.addEventListener('click', stopScanner);
</script>
@endsection