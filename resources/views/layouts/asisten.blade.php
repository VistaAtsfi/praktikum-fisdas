<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Asisten' }} - Praktikum Fisika Dasar</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden"
      x-data="{ sidebarOpen: false, logoutModal: false }"
      @keydown.escape.window="sidebarOpen = false; logoutModal = false">

    <div class="min-h-screen flex w-full">

        <div x-cloak
             x-show="sidebarOpen"
             x-transition.opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-900/50 z-30 lg:hidden">
        </div>

        <aside x-cloak
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-blue-100 flex flex-col transform transition-transform duration-300 ease-in-out">

            <div class="h-20 px-6 flex items-center justify-between border-b border-blue-100">
                <a href="{{ route('dashboard.asisten') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center overflow-hidden shadow-sm">
                        <img src="{{ asset('assets/images/LogoUnmul.svg') }}"
                            alt="Logo Universitas Mulawarman"
                            class="w-8 h-8 object-contain">
                    </div>

                    <div>
                        <h1 class="font-extrabold text-blue-900 leading-tight">
                            Praktikum Fisika Dasar
                        </h1>
                        <p class="text-xs text-slate-500">Panel Asisten</p>
                    </div>
                </a>

                <button type="button"
                        @click="sidebarOpen = false"
                        class="lg:hidden w-9 h-9 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition">
                    ×
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('dashboard.asisten') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('dashboard.asisten') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>🏠</span>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('asisten.presensi') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('asisten.presensi') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>✅</span>
                    <span>Presensi Praktikan</span>
                </a>

                <a href="{{ route('asisten.materi') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('asisten.materi') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>📚</span>
                    <span>Materi</span>
                </a>

                <a href="{{ route('asisten.jadwal') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('asisten.jadwal') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>📅</span>
                    <span>Jadwal Praktikum</span>
                </a>

                <a href="{{ route('asisten.penilaian') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('asisten.penilaian*') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>📝</span>
                    <span>Penilaian</span>
                </a>
            </nav>

            <div class="p-4 border-t border-blue-100">
                <button type="button"
                        @click="logoutModal = true"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-red-600 text-white font-bold hover:bg-red-700 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.4">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14 7V5.25A2.25 2.25 0 0011.75 3h-5.5A2.25 2.25 0 004 5.25v13.5A2.25 2.25 0 006.25 21h5.5A2.25 2.25 0 0014 18.75V17" />
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 12h10" />
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 8l4 4-4 4" />
                    </svg>

                    <span>Keluar</span>
                </button>
            </div>
        </aside>

        <div class="flex-1 lg:ml-72 min-w-0 max-w-full">
            <header class="h-20 bg-white/90 backdrop-blur border-b border-blue-100 sticky top-0 z-20">
                <div class="h-full px-6 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg md:text-xl font-extrabold text-slate-900 leading-tight">
                            {{ $title ?? 'Panel Asisten' }}
                        </h2>
                        <p class="text-sm text-slate-500">
                            {{ auth()->user()->name }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Switcher Praktikum --}}
                        @php
                            $daftarPraktikums = \App\Models\Praktikum::orderBy('id')->get();
                            $activePraktikumId = $praktikum->id ?? session('asisten_praktikum_id') ?? $daftarPraktikums->first()?->id;
                        @endphp
                        @if($daftarPraktikums->count() > 1)
                            <form method="POST" action="{{ route('asisten.switch-praktikum') }}" class="flex items-center">
                                @csrf
                                <div class="relative flex items-center">
                                    <label for="asisten-switch-praktikum-select" class="sr-only">Pilih Praktikum</label>
                                    <div class="absolute left-3 flex items-center pointer-events-none text-blue-700">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    <select id="asisten-switch-praktikum-select"
                                            name="praktikum_id"
                                            onchange="this.form.submit()"
                                            class="pl-9 pr-8 py-2 text-xs md:text-sm font-bold bg-blue-50 hover:bg-blue-100/70 border border-blue-200 text-blue-900 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer shadow-xs transition appearance-none">
                                        @foreach($daftarPraktikums as $p)
                                            <option value="{{ $p->id }}" @selected($activePraktikumId == $p->id)>
                                                {{ $p->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <svg class="w-4 h-4 text-blue-600 absolute right-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </form>
                        @endif

                        <button type="button"
                                @click="sidebarOpen = true"
                                class="lg:hidden px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                            Menu
                        </button>

                        <div class="hidden sm:flex items-center gap-3 bg-blue-50 border border-blue-100 px-4 py-2 rounded-2xl">
                            <div class="w-9 h-9 rounded-xl bg-blue-700 text-white flex items-center justify-center font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>

                            <div class="text-sm">
                                <p class="font-bold text-slate-800">{{ auth()->user()->email }}</p>
                                <p class="text-slate-500">Asisten Praktikum</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-6 md:p-8">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl bg-green-50 border border-green-100 text-green-700 px-5 py-4 font-semibold">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Modal Konfirmasi Logout --}}
    <div x-cloak
         x-show="logoutModal"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
        <div @click.outside="logoutModal = false"
             x-transition
             class="w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-red-100 overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-4 border border-red-100 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>

                <h2 class="text-base font-bold text-slate-800">
                    Apakah Anda yakin ingin keluar dari sistem?
                </h2>
            </div>

            <div class="px-6 pb-6 grid grid-cols-2 gap-3">
                <button type="button"
                        @click="logoutModal = false"
                        class="px-4 py-2.5 rounded-2xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition cursor-pointer text-sm">
                    Batal
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2.5 rounded-2xl bg-red-600 text-white font-bold hover:bg-red-700 transition shadow-xs cursor-pointer text-sm">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>