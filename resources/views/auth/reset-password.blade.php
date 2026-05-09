<x-guest-layout>
    <div class="mb-7 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 text-2xl mb-4">
            🔑
        </div>

        <h2 class="text-2xl font-extrabold text-slate-900">
            Reset Password
        </h2>

        <p class="text-sm text-slate-500 mt-2">
            Masukkan password baru untuk akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input id="email"
                class="block mt-1 w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-password-input
                id="password"
                name="password"
                label="Password Baru"
                autocomplete="new-password"
            />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-password-input
                id="password_confirmation"
                name="password_confirmation"
                label="Konfirmasi Password Baru"
                autocomplete="new-password"
            />
        </div>

        <button type="submit"
            class="w-full mt-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 shadow-lg shadow-blue-700/20 transition">
            Simpan Password Baru
        </button>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}"
               class="text-sm font-extrabold text-blue-700 hover:underline">
                Kembali ke Login
            </a>
        </div>
    </form>
</x-guest-layout>