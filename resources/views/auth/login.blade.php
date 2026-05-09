<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Masuk Sistem</h2>
        <p class="text-sm text-gray-600 mt-1">
            Praktikum Fisika Dasar
        </p>
    </div>

    <a href="{{ route('google.redirect') }}"
       class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-md px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 mb-5">
        <span class="text-lg">G</span>
        <span>Masuk dengan Google</span>
    </a>

    <div class="flex items-center mb-5">
        <div class="flex-1 border-t border-gray-300"></div>
        <span class="px-3 text-sm text-gray-500">atau</span>
        <div class="flex-1 border-t border-gray-300"></div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                    name="remember">

                <span class="ms-2 text-sm text-gray-600">
                    Ingat saya
                </span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900"
                   href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif

            <x-primary-button>
                Masuk
            </x-primary-button>
        </div>

        <div class="mt-5 text-center">
            <span class="text-sm text-gray-600">Belum punya akun?</span>

            <a href="{{ route('register') }}"
               class="text-sm font-semibold text-indigo-600 hover:underline">
                Daftar
            </a>
        </div>
    </form>
</x-guest-layout>