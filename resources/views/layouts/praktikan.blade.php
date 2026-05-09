<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Praktikan' }} - Praktikum Fisika Dasar</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 text-slate-800">
    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:w-72 bg-white border-r border-blue-100 flex-col fixed inset-y-0 left-0 z-40">
            <div class="h-20 px-6 flex items-center border-b border-blue-100">
                <a href="{{ route('dashboard.praktikan') }}" class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-blue-700 text-white flex items-center justify-center text-xl shadow">
                        ⚛
                    </div>

                    <div>
                        <h1 class="font-extrabold text-blue-900 leading-tight">
                            Praktikum Fisika Dasar
                        </h1>
                        <p class="text-xs text-slate-500">Panel Praktikan</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="{{ route('dashboard.praktikan') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('dashboard.praktikan') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>🏠</span>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('praktikan.materi') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('praktikan.materi') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>📚</span>
                    <span>Materi Praktikum</span>
                </a>

                <a href="{{ route('praktikan.jadwal') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('praktikan.jadwal') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>🗓️</span>
                    <span>Jadwal</span>
                </a>

                <a href="{{ route('praktikan.biodata') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition {{ request()->routeIs('praktikan.biodata') ? 'bg-blue-700 text-white shadow' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                    <span>👤</span>
                    <span>Biodata Diri</span>
                </a>
            </nav>

            <div class="p-4 border-t border-blue-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-red-600 text-white font-bold hover:bg-red-700 transition">
                        <span>🚪</span>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 lg:ml-72">
            <!-- Topbar -->
            <header class="h-20 bg-white/90 backdrop-blur border-b border-blue-100 sticky top-0 z-30">
                <div class="h-full px-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg md:text-xl font-extrabold text-slate-900">
                            {{ $title ?? 'Panel Praktikan' }}
                        </h2>
                        <p class="text-sm text-slate-500">
                            {{ auth()->user()->name }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('dashboard.praktikan') }}"
                           class="lg:hidden px-4 py-2 rounded-xl bg-blue-700 text-white font-bold">
                            Menu
                        </a>

                        <div class="hidden sm:flex items-center gap-3 bg-blue-50 border border-blue-100 px-4 py-2 rounded-2xl">
                            <div class="w-9 h-9 rounded-xl bg-blue-700 text-white flex items-center justify-center font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>

                            <div class="text-sm">
                                <p class="font-bold text-slate-800">{{ auth()->user()->nim }}</p>
                                <p class="text-slate-500">{{ auth()->user()->prodi }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-6 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>