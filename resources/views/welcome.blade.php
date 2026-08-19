<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Praktikum Fisika Dasar</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/LogoUnmul.svg') }}">
        <link rel="shortcut icon" href="{{ asset('assets/images/LogoUnmul.svg') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-slate-50 text-slate-800">

    @php
        $drive = [
            'video_k3' => [
                'preview' => 'https://drive.google.com/file/d/1yUxOYbG0CxQGIsVhDoWVqvUezku1QZsn/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=1yUxOYbG0CxQGIsVhDoWVqvUezku1QZsn',
            ],
            'pedoman_k3' => [
                'preview' => 'https://drive.google.com/file/d/1X4whYXkWgK8AamG4MYUFOemQjW9yTZoa/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=1X4whYXkWgK8AamG4MYUFOemQjW9yTZoa',
            ],
            'sop_praktikum' => [
                'preview' => 'https://drive.google.com/file/d/10iU3Gb0G3j8RfgekjLoRqL3WBb8QlHNi/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=10iU3Gb0G3j8RfgekjLoRqL3WBb8QlHNi',
            ],
        ];

        $atributs = [
            [
                'judul' => 'Lembar Penilaian Kemampuan Praktikum',
                'deskripsi' => 'Form penilaian kemampuan praktikum pada setiap modul.',
                'icon' => '🧪',
                'preview' => 'https://drive.google.com/file/d/1MPZ1hQiT2csCRPAc-sZbdr7-PjuasylQ/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=1MPZ1hQiT2csCRPAc-sZbdr7-PjuasylQ',
            ],
            [
                'judul' => 'Lembar Penilaian Laporan Praktikum',
                'deskripsi' => 'Form penilaian laporan praktikum praktikan.',
                'icon' => '📋',
                'preview' => 'https://drive.google.com/file/d/1snBMaaLRHCAYD3aVgatERVr1wO3MWLOO/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=1snBMaaLRHCAYD3aVgatERVr1wO3MWLOO',
            ],
            [
                'judul' => 'Lembar Konsultasi Laporan Praktikum',
                'deskripsi' => 'Form konsultasi laporan praktikum dengan asisten.',
                'icon' => '📝',
                'preview' => 'https://drive.google.com/file/d/1MdyvwX3wkHs9O5k6_joi6tWA-F7m1_4N/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=1MdyvwX3wkHs9O5k6_joi6tWA-F7m1_4N',
            ],
            [
                'judul' => 'Cover Laporan Mingguan',
                'deskripsi' => 'Template cover laporan mingguan praktikum.',
                'icon' => '📘',
                'preview' => 'https://drive.google.com/file/d/1aG1jf4-E8iXXXhxh8vroGKF2kPpYtmmO/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=1aG1jf4-E8iXXXhxh8vroGKF2kPpYtmmO',
            ],
            [
                'judul' => 'Lembar Persetujuan Laporan Praktikum',
                'deskripsi' => 'Lembar persetujuan untuk laporan praktikum.',
                'icon' => '✅',
                'preview' => 'https://drive.google.com/file/d/1Q84tZOttkGTuMMj4wnORyh2ntBs2Xdt6/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=1Q84tZOttkGTuMMj4wnORyh2ntBs2Xdt6',
            ],
            [
                'judul' => 'Cover Laporan Resmi Fisdas',
                'deskripsi' => 'Template cover laporan resmi Praktikum Fisika Dasar.',
                'icon' => '📄',
                'preview' => 'https://drive.google.com/file/d/1zyJOE4pcQsXOsycF7ff1h-zuOcC7bjxi/preview',
                'download' => 'https://drive.google.com/uc?export=download&id=1zyJOE4pcQsXOsycF7ff1h-zuOcC7bjxi',
            ],
        ];
    @endphp

    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-blue-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white flex items-center justify-center shadow-md overflow-hidden">
                    <img src="{{ asset('assets/images/LogoUnmul.svg') }}"
                        alt="Logo Universitas Mulawarman"
                        class="w-9 h-9 object-contain">
                </div>

                <div>
                    <h1 class="text-lg md:text-xl font-extrabold text-blue-900 leading-tight">
                        Praktikum Fisika Dasar
                    </h1>
                    <p class="text-xs md:text-sm text-slate-500">
                        Laboratorium Layanan Dasar
                    </p>
                </div>
            </a>

            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-700">
                <a href="#login-role" class="hover:text-blue-700 transition">Login</a>
                <a href="#k3" class="hover:text-blue-700 transition">Pedoman K3</a>
                <a href="#sop" class="hover:text-blue-700 transition">SOP</a>
                <a href="#atribut" class="hover:text-blue-700 transition">Atribut</a>
            </nav>

            {{-- Login dan Regis di navbar --}}
            {{-- <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}"
                    class="px-4 py-2 rounded-xl text-sm font-bold text-blue-700 border border-blue-200 hover:bg-blue-50 transition">
                        Login
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                        class="hidden sm:inline-block px-4 py-2 rounded-xl text-sm font-bold text-white bg-blue-700 hover:bg-blue-800 shadow transition">
                            Daftar Praktikan
                        </a>
                    @endif
                @endguest

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow transition">
                            Logout
                        </button>
                    </form>
                @endauth
            </div> --}}
        </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-950 via-blue-800 to-cyan-700 text-white">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute -top-20 -left-20 w-80 h-80 rounded-full bg-cyan-300 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full bg-blue-300 blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 py-12 md:py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/20 text-sm mb-6">
                    <span>Portal Sistem Praktikum Fisika Dasar FT UNMUL</span>
                </div>

                <h2 class="text-4xl md:text-5xl font-extrabold leading-tight mb-5">
                    Praktikum Fisika Dasar <br>
                    FT UNMUL
                </h2>

                <p class="text-blue-100 text-base md:text-lg leading-relaxed mb-7">
                    Sistem praktikum untuk pengelolaan data praktikan,
                    presensi QR, penilaian, materi, serta informasi K3 dan SOP praktikum.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#login-role"
                        class="px-7 py-3 rounded-2xl bg-white text-blue-800 font-extrabold text-center hover:bg-blue-50 shadow-lg transition">
                            Pilih Akses Login
                    </a>

                    <a href="#k3"
                        class="px-7 py-3 rounded-2xl border border-white/30 text-white font-extrabold text-center hover:bg-white/10 transition">
                            Lihat Pedoman K3
                    </a>
                </div>
            </div>

            <div class="bg-white/10 border border-white/20 rounded-3xl p-6 shadow-2xl backdrop-blur">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Fisika Dasar 1 -->
                    <div class="bg-white/10 rounded-2xl p-6 border border-white/10 min-h-[160px] flex flex-col items-center justify-center text-center hover:bg-white/15 transition">
                        <div class="text-[46px] mb-4">🧲</div>

                        <h3 class="font-extrabold text-[18px] mb-3">
                            Fisika Dasar 1
                        </h3>

                        <p class="text-[13px] md:text-[14px] text-blue-100 leading-relaxed max-w-[220px]">
                            Modul, presensi, dan penilaian praktikum terintegrasi.
                        </p>
                    </div>

                    <!-- Fisika Dasar 2 -->
                    <div class="bg-white/10 rounded-2xl p-6 border border-white/10 min-h-[160px] flex flex-col items-center justify-center text-center hover:bg-white/15 transition">
                        <div class="text-[46px] mb-4">🔬</div>

                        <h3 class="font-extrabold text-[18px] mb-3">
                            Fisika Dasar 2
                        </h3>

                        <p class="text-[13px] md:text-[14px] text-blue-100 leading-relaxed max-w-[220px]">
                            Mendukung pengelolaan praktikum sesuai jenis dan periode.
                        </p>
                    </div>

                    <!-- Presensi QR -->
                    <div class="bg-white/10 rounded-2xl p-6 border border-white/10 min-h-[160px] flex flex-col items-center justify-center text-center hover:bg-white/15 transition">
                        <div class="text-[46px] mb-4">📱</div>

                        <h3 class="font-extrabold text-[18px] mb-3">
                            Presensi QR
                        </h3>

                        <p class="text-[13px] md:text-[14px] text-blue-100 leading-relaxed max-w-[220px]">
                            Asisten melakukan scan QR untuk mencatat kehadiran praktikan.
                        </p>
                    </div>

                    <!-- Rekap Nilai -->
                    <div class="bg-white/10 rounded-2xl p-6 border border-white/10 min-h-[160px] flex flex-col items-center justify-center text-center hover:bg-white/15 transition">
                        <div class="text-[46px] mb-4">📊</div>

                        <h3 class="font-extrabold text-[18px] mb-3">
                            Rekap Nilai
                        </h3>

                        <p class="text-[13px] md:text-[14px] text-blue-100 leading-relaxed max-w-[220px]">
                            Rekap nilai laporan, harian, kemampuan praktik, dan nilai akhir.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Role -->
    <section id="login-role" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <p class="text-blue-700 font-bold mb-2">AKSES PENGGUNA</p>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">
                    Masuk Sesuai Role Pengguna
                </h2>
                <p class="text-slate-600 mt-4">
                    Sistem menggunakan satu halaman login. Setelah berhasil masuk,
                    pengguna akan diarahkan otomatis sesuai role masing-masing.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Praktikan -->
                <div class="bg-white rounded-3xl shadow-sm border border-blue-100 p-8 hover:shadow-xl hover:-translate-y-1 transition flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-3xl mb-6">
                        🎓
                    </div>

                    <h3 class="text-2xl font-extrabold text-slate-900 mb-3">
                        Praktikan
                    </h3>

                    <p class="text-slate-600 leading-relaxed mb-6 max-w-[360px]">
                        Akses untuk melihat QR Code, presensi,
                        materi praktikum, dan nilai praktikum.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full">
                        <a href="{{ route('login.praktikan') }}"
                        class="block text-center py-3 rounded-2xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                        class="block text-center py-3 rounded-2xl border border-blue-700 text-blue-700 font-bold hover:bg-blue-50 transition">
                            Daftar
                        </a>
                    </div>

                    @guest
                        <div class="text-center mt-1">
                            <p class="text-xs text-slate-500 mt-4 text-center">
                                Belum punya akun praktikan?
                                <a href="{{ route('register') }}" class="text-blue-700 font-extrabold hover:underline">
                                    Daftar sekarang
                                </a>
                            </p>
                        </div>
                    @endguest
                    
                </div>

                <!-- Asisten -->
                <div class="bg-white rounded-3xl shadow-sm border border-blue-100 p-8 hover:shadow-xl hover:-translate-y-1 transition flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-cyan-100 text-cyan-700 flex items-center justify-center text-3xl mb-6">
                        🧑‍🏫
                    </div>

                    <h3 class="text-2xl font-extrabold text-slate-900 mb-3">
                        Asisten
                    </h3>

                    <p class="text-slate-600 leading-relaxed mb-6 max-w-[360px]">
                        Akses untuk scan QR presensi, melihat data
                        praktikan, dan input nilai praktikum.
                    </p>

                    <a href="{{ route('login.asisten') }}"
                    class="block text-center w-full py-3 rounded-2xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition">
                        Login Asisten
                    </a>

                    <p class="text-xs text-slate-500 mt-4 text-center">
                        Akun asisten dibuat oleh koordinator.
                    </p>
                </div>

                <!-- Koordinator -->
                <div class="bg-white rounded-3xl shadow-sm border border-blue-100 p-8 hover:shadow-xl hover:-translate-y-1 transition flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-3xl mb-6">
                        🗂️
                    </div>

                    <h3 class="text-2xl font-extrabold text-slate-900 mb-3">
                        Koordinator
                    </h3>

                    <p class="text-slate-600 leading-relaxed mb-6 max-w-[360px]">
                        Akses untuk mengelola praktikum, asisten,
                        praktikan, modul, materi, dan rekap nilai.
                    </p>

                    <a href="{{ route('login.koordinator') }}"
                    class="block text-center w-full py-3 rounded-2xl bg-indigo-700 text-white font-bold hover:bg-indigo-800 transition">
                        Login Koordinator
                    </a>

                    <p class="text-xs text-slate-500 mt-4 text-center">
                        Akun koordinator dibuat oleh sistem/admin.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pedoman K3 -->
    <section id="k3" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <p class="text-blue-700 font-bold mb-2">KESELAMATAN LABORATORIUM</p>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">
                    Pedoman K3 Laboratorium Layanan Dasar
                </h2>
                <p class="text-slate-600 mt-4">
                    Praktikan wajib memahami pedoman K3 sebelum mengikuti kegiatan praktikum.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Video K3 -->
                <div class="rounded-3xl overflow-hidden bg-slate-100 border border-blue-100 shadow-sm">
                    <div class="bg-blue-950 px-6 py-4">
                        <h3 class="font-extrabold text-white">Video K3 Laboratorium</h3>
                    </div>

                    <iframe
                        src="{{ $drive['video_k3']['preview'] }}"
                        class="w-full h-[420px] bg-slate-900"
                        allow="autoplay"
                        allowfullscreen>
                    </iframe>

                    <div class="p-6 bg-white flex flex-col items-center text-center">
                        <p class="text-slate-600 mb-4">
                            Video panduan keselamatan gedung dan prosedur keselamatan praktikum.
                        </p>

                        <a href="{{ $drive['video_k3']['download'] }}"
                        class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                            Download Video
                        </a>
                    </div>
                </div>

                <!-- PDF Pedoman K3 -->
                <div class="rounded-3xl overflow-hidden bg-slate-100 border border-blue-100 shadow-sm">
                    <div class="bg-blue-950 px-6 py-4">
                        <h3 class="font-extrabold text-white">PDF Pedoman K3</h3>
                    </div>

                    <iframe
                        src="{{ $drive['pedoman_k3']['preview'] }}"
                        class="w-full h-[420px] bg-white">
                    </iframe>

                    <div class="p-6 bg-white flex flex-col items-center text-center">
                        <p class="text-slate-600 mb-4">
                            Dokumen PEDOMAN K3 LABORATORIUM LAYANAN DASAR.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ $drive['pedoman_k3']['preview'] }}"
                            target="_blank"
                            class="px-5 py-3 rounded-xl bg-slate-700 text-white font-bold text-center hover:bg-slate-800 transition">
                                Lihat PDF
                            </a>

                            <a href="{{ $drive['pedoman_k3']['download'] }}"
                            class="px-5 py-3 rounded-xl bg-blue-700 text-white font-bold text-center hover:bg-blue-800 transition">
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SOP Praktikum -->
    <section id="sop" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <p class="text-blue-700 font-bold mb-2">PERATURAN PRAKTIKUM</p>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">
                    SOP Praktikum dan Sanksi
                </h2>
                <p class="text-slate-600 mt-4">
                    Praktikan dan asisten wajib memahami SOP, aturan, dan konsekuensi pelanggaran.
                </p>
            </div>

            <div class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden mb-10">
                <div class="bg-blue-950 px-6 py-4">
                    <h3 class="font-extrabold text-white">Dokumen SOP Praktikum</h3>
                </div>

                <iframe
                    src="{{ $drive['sop_praktikum']['preview'] }}"
                    class="w-full h-[620px] bg-white">
                </iframe>

                <div class="p-6 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ $drive['sop_praktikum']['preview'] }}"
                    target="_blank"
                    class="px-6 py-3 rounded-xl bg-slate-700 text-white font-bold text-center hover:bg-slate-800 transition">
                        Lihat SOP
                    </a>

                    <a href="{{ $drive['sop_praktikum']['download'] }}"
                    class="px-6 py-3 rounded-xl bg-blue-700 text-white font-bold text-center hover:bg-blue-800 transition">
                        Download SOP
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Sanksi -->
                <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-8">
                    <div class="flex items-center justify-center gap-3 mb-6 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-2xl">
                            ⚠️
                        </div>

                        <h3 class="text-2xl font-extrabold text-slate-900">Sanksi</h3>
                    </div>

                    <ul class="space-y-4 text-slate-700 leading-relaxed">
                        <li><strong>1.</strong> Tidak mengumpulkan laporan mingguan sesuai deadline: sanksi point 2.</li>
                        <li><strong>2.</strong> Copy paste hasil laporan praktikum milik praktikan lain: sanksi point 2.</li>
                        <li><strong>3.</strong> Datang terlambat 15 menit dari waktu yang ditentukan: sanksi point 3.</li>
                        <li><strong>4.</strong> Tidak membawa modul dan lembar penilaian praktikum: sanksi point 5.</li>
                        <li><strong>5.</strong> Pemalsuan tanda tangan, manipulasi absensi, data palsu, dan kecurangan lain: sanksi point 1.</li>
                        <li><strong>6.</strong> Merusakkan peralatan Laboratorium Elektro Dasar: sanksi point 4.</li>
                        <li><strong>7.</strong> Tidak mematuhi peraturan dan SOP praktikum: sanksi point 5.</li>
                        <li><strong>8.</strong> Tidak mengumpulkan scan surat pernyataan praktikum: sanksi point 6.</li>
                        <li><strong>9.</strong> Asisten terlambat tanpa konfirmasi: sanksi point 7.</li>
                        <li><strong>10.</strong> Menghilangkan lembar penilaian atau laporan praktikum: sanksi point 8.</li>
                    </ul>
                </div>

                <!-- Point Sanksi -->
                <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-8">
                    <div class="flex items-center justify-center gap-3 mb-6 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl">
                            📌
                        </div>

                        <h3 class="text-2xl font-extrabold text-slate-900">Point Sanksi</h3>
                    </div>

                    <div class="space-y-4 text-slate-700 leading-relaxed">
                        <p><strong>Point 1:</strong> Mengulang praktikum atau mendapat nilai E bagi yang melakukan dan membantu kecurangan.</p>
                        <p><strong>Point 2:</strong> Laporan tidak ACC atau mendapat nilai 0.</p>
                        <p><strong>Point 3:</strong> Tidak diperbolehkan mengikuti praktikum modul yang dipraktikumkan.</p>
                        <p><strong>Point 4:</strong> Mengganti peralatan sesuai spesifikasi dan fungsi yang sama.</p>
                        <p><strong>Point 5:</strong> Tidak diperbolehkan mengikuti praktikum.</p>
                        <p><strong>Point 6:</strong> Tidak diperbolehkan mengikuti responsi praktikum.</p>
                        <p><strong>Point 7:</strong> Praktikan diperbolehkan pulang dan asisten wajib menjadwalkan ulang praktikum pada minggu yang sama.</p>
                        <p><strong>Point 8:</strong> Nilai pada lembar yang hilang diberi 0 dan laporan wajib dibuat kembali sesuai peraturan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Atribut Praktikum -->
    <section id="atribut" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <p class="text-blue-700 font-bold mb-2">DOKUMEN PENDUKUNG</p>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900">
                    Atribut Praktikum
                </h2>
                <p class="text-slate-600 mt-4">
                    Dokumen pendukung praktikum dapat dilihat dan diunduh melalui Google Drive.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($atributs as $atribut)
                    <div class="group bg-slate-50 rounded-3xl border border-blue-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition">
                        <div class="p-8 bg-gradient-to-br from-blue-50 to-cyan-50 flex flex-col items-center text-center">
                            <div class="w-16 h-16 rounded-2xl bg-white text-blue-700 flex items-center justify-center text-3xl shadow-sm mb-6">
                                {{ $atribut['icon'] }}
                            </div>

                            <h3 class="text-xl font-extrabold text-slate-900 mb-3 min-h-[56px]">
                                {{ $atribut['judul'] }}
                            </h3>

                            <p class="text-slate-600 leading-relaxed min-h-[72px] max-w-[360px]">
                                {{ $atribut['deskripsi'] }}
                            </p>
                        </div>

                        <div class="p-6 bg-white grid grid-cols-2 gap-3">
                            <a href="{{ $atribut['preview'] }}"
                            target="_blank"
                            class="py-3 rounded-xl bg-slate-700 text-white text-center font-bold hover:bg-slate-800 transition">
                                Lihat
                            </a>

                            <a href="{{ $atribut['download'] }}"
                            class="py-3 rounded-xl bg-blue-700 text-white text-center font-bold hover:bg-blue-800 transition">
                                Download
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-950 text-white">
        <div class="max-w-7xl mx-auto px-6 py-10">
            <div class="flex flex-col md:flex-row justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-11 h-11 rounded-2xl bg-white flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('assets/images/LogoUnmul.svg') }}"
                                alt="Logo Universitas Mulawarman"
                                class="w-9 h-9 object-contain">
                        </div>

                        <div>
                            <h3 class="text-xl font-extrabold">Praktikum Fisika Dasar</h3>
                            <p class="text-blue-200 text-sm">Laboratorium Layanan Dasar</p>
                        </div>
                    </div>

                    <p class="text-blue-100 max-w-xl">
                        Sistem pengelolaan praktikum berbasis web untuk mendukung presensi,
                        penilaian, materi, dan informasi praktikum.
                    </p>
                </div>

                <div class="text-blue-100 text-sm md:text-right">
                    <p>Fakultas Teknik</p>
                    <p>Universitas Mulawarman</p>
                    <p class="mt-4 text-blue-300">© {{ date('Y') }} Praktikum Fisika Dasar</p>
                </div>
            </div>
        </div>
    </footer>

    </body>
</html>