<x-guest-layout>
    <div class="mb-7 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 text-2xl mb-4">
            ✉️
        </div>

        <h2 class="text-2xl font-extrabold text-slate-900">
            Lupa Password
        </h2>

        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Masukkan email akun Anda. Sistem akan mengirimkan link untuk membuat password baru.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input id="email"
                class="block mt-1 w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                placeholder="Masukkan email akun Anda" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit"
            class="w-full mt-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 shadow-lg shadow-blue-700/20 transition">
            Kirim Link Reset Password
        </button>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}"
               class="text-sm font-extrabold text-blue-700 hover:underline">
                Kembali ke Login
            </a>
        </div>
    </form>
</x-guest-layout>