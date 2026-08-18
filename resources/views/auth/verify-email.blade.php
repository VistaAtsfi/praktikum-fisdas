<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Terima kasih sudah mendaftar. Sebelum masuk ke sistem, silakan verifikasi alamat email Anda
        dengan membuka link verifikasi yang sudah dikirim ke email.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Link verifikasi baru sudah dikirim ke email Anda.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                Kirim Ulang Email Verifikasi
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
            @csrf

            <button type="submit"
                class="underline text-sm text-gray-600 hover:text-gray-900">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>