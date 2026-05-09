<x-guest-layout>
    <div class="mb-7 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 text-2xl mb-4">
            🎓
        </div>

        <h2 class="text-2xl font-extrabold text-slate-900">
            Daftar Akun Praktikan
        </h2>

        <p class="text-sm text-slate-500 mt-2">
            Buat akun terlebih dahulu, lalu verifikasi email dan lengkapi data praktikan.
        </p>
    </div>

    <a href="{{ route('google.redirect') }}"
       class="w-full flex items-center justify-center gap-3 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 hover:border-blue-200 transition mb-5">
        <span class="w-7 h-7 rounded-full bg-white border border-slate-200 flex items-center justify-center text-blue-700 font-extrabold">
            G
        </span>
        <span>Daftar dengan Google</span>
    </a>

    <div class="flex items-center mb-5">
        <div class="flex-1 border-t border-slate-200"></div>
        <span class="px-3 text-xs font-semibold text-slate-400">
            atau daftar dengan email
        </span>
        <div class="flex-1 border-t border-slate-200"></div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Email -->
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
                placeholder="Masukkan email aktif" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-password-input
                id="password"
                name="password"
                label="Password"
                autocomplete="new-password"
            />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-password-input
                id="password_confirmation"
                name="password_confirmation"
                label="Konfirmasi Password"
                autocomplete="new-password"
            />
        </div>

        <button type="submit"
            class="w-full mt-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 shadow-lg shadow-blue-700/20 transition">
            Daftar
        </button>

        <div class="mt-6 text-center">
            <span class="text-sm text-slate-500">
                Sudah punya akun?
            </span>

            <a href="{{ route('login') }}"
               class="text-sm font-extrabold text-blue-700 hover:underline">
                Masuk
            </a>
        </div>
    </form>
</x-guest-layout>