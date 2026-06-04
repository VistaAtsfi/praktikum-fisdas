<x-guest-layout>
    @php
        $loginRole = $loginRole ?? null;

        $isPraktikanLogin = $loginRole === 'praktikan';
        $isAsistenLogin = $loginRole === 'asisten';
        $isKoordinatorLogin = $loginRole === 'koordinator';

        $judulLogin = match ($loginRole) {
            'praktikan' => 'Masuk Praktikan',
            'asisten' => 'Masuk Asisten',
            'koordinator' => 'Masuk Koordinator',
            default => 'Masuk ke Sistem',
        };

        $deskripsiLogin = match ($loginRole) {
            'praktikan' => 'Gunakan akun praktikan yang sudah terdaftar.',
            'asisten' => 'Gunakan akun asisten yang sudah dibuat oleh koordinator.',
            'koordinator' => 'Gunakan akun koordinator untuk mengelola sistem praktikum.',
            default => 'Gunakan akun yang sudah terdaftar untuk mengakses dashboard.',
        };

        /*
            Google dan daftar hanya ditampilkan untuk login praktikan.
            Asisten dan koordinator hanya login email & password + lupa password.
        */
        $tampilkanGoogle = $isPraktikanLogin;
        $tampilkanDaftar = $isPraktikanLogin;
    @endphp

    <div class="mb-7 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 text-2xl mb-4">
            🔐
        </div>

        <h2 class="text-2xl font-extrabold text-slate-900">
            {{ $judulLogin }}
        </h2>

        <p class="text-sm text-slate-500 mt-2">
            {{ $deskripsiLogin }}
        </p>
    </div>

    @if ($tampilkanGoogle)
        <a href="{{ route('google.redirect') }}"
           class="w-full flex items-center justify-center gap-3 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:border-blue-200 transition mb-5">
            <span class="w-7 h-7 rounded-full bg-white border border-slate-200 flex items-center justify-center text-blue-700 font-extrabold">
                G
            </span>
            <span>Masuk dengan Google</span>
        </a>

        <div class="flex items-center mb-5">
            <div class="flex-1 border-t border-slate-200"></div>
            <span class="px-3 text-xs font-semibold text-slate-400">
                atau masuk dengan email
            </span>
            <div class="flex-1 border-t border-slate-200"></div>
        </div>
    @endif

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input id="email"
                class="block mt-1 w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan email" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-password-input
                id="password"
                name="password"
                label="Password"
                autocomplete="current-password"
            />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me"
                    type="checkbox"
                    class="rounded border-slate-300 text-blue-700 shadow-sm focus:ring-blue-500"
                    name="remember">

                <span class="ms-2 text-sm text-slate-600">
                    Ingat saya
                </span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-blue-700 hover:text-blue-900 hover:underline"
                   href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit"
            class="w-full mt-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 shadow-lg shadow-blue-700/20 transition">
            Masuk
        </button>

        @if ($tampilkanDaftar)
            <div class="mt-6 text-center">
                <span class="text-sm text-slate-500">
                    Belum punya akun praktikan?
                </span>

                <a href="{{ route('register') }}"
                   class="text-sm font-extrabold text-blue-700 hover:underline">
                    Daftar
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>