<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl mb-3 shadow-xs">
            ✉️
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900">Verifikasi Email</h2>
        <p class="text-sm text-slate-500 mt-1 leading-relaxed">
            Terima kasih sudah mendaftar. Silakan klik link verifikasi yang telah dikirimkan ke email Anda.
        </p>
    </div>

    <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-800 leading-relaxed flex items-start gap-2.5">
        <span class="text-base shrink-0">💡</span>
        <span><strong>Tips:</strong> Jika email verifikasi tidak muncul di folder <em>Kotak Masuk (Inbox)</em>, mohon periksa folder <strong>Spam / Promosi / Sampah</strong> di akun Gmail Anda.</span>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl font-bold text-xs text-emerald-800 flex items-center gap-2">
            <span class="text-base">✓</span>
            <span>Link verifikasi baru telah berhasil dikirim ke email Anda.</span>
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
                class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-700 text-white font-extrabold text-sm shadow-lg shadow-blue-700/25 hover:from-blue-800 hover:to-indigo-800 hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
            <span>🔄</span>
            <span>Kirim Ulang Email Verifikasi</span>
        </button>
    </form>

    <div class="mt-5 text-center">
        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
            @csrf
            <button type="submit" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition underline cursor-pointer">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>