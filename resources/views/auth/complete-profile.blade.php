<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-2xl mb-3 shadow-xs">
            📝
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900">Lengkapi Data Praktikan</h2>
        <p class="text-sm text-slate-500 mt-1">
            Data ini diperlukan sebelum masuk ke dashboard praktikan.
        </p>
    </div>

    <form method="POST" action="{{ route('profile.complete.update') }}" class="space-y-4">
        @csrf
        @method('PATCH')

        <!-- Praktikum -->
        <div>
            <x-input-label for="praktikum_id" :value="__('Jenis Praktikum')" class="font-bold text-slate-700 text-xs uppercase tracking-wider" />

            <select id="praktikum_id"
                name="praktikum_id"
                class="block mt-1 w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-xs"
                required>
                <option value="">-- Pilih Praktikum --</option>

                @foreach ($praktikums as $praktikum)
                    <option value="{{ $praktikum->id }}" {{ old('praktikum_id') == $praktikum->id ? 'selected' : '' }}>
                        {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }} - {{ ucfirst($praktikum->semester) }}
                    </option>
                @endforeach
            </select>

            <x-input-error :messages="$errors->get('praktikum_id')" class="mt-2" />
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="font-bold text-slate-700 text-xs uppercase tracking-wider" />

            <x-text-input id="name"
                class="block mt-1 w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-xs"
                type="text"
                name="name"
                :value="old('name', auth()->user()->name === 'Pengguna Baru' ? '' : auth()->user()->name)"
                required
                autofocus
                placeholder="Masukkan nama lengkap" />

            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- NIM -->
        <div>
            <x-input-label for="nim" :value="__('NIM')" class="font-bold text-slate-700 text-xs uppercase tracking-wider" />

            <x-text-input id="nim"
                class="block mt-1 w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-xs"
                type="text"
                name="nim"
                :value="old('nim', auth()->user()->nim)"
                required
                placeholder="Contoh: 2509076001" />

            <x-input-error :messages="$errors->get('nim')" class="mt-2" />
        </div>

        <!-- Prodi -->
        <div>
            <x-input-label for="prodi" :value="__('Program Studi')" class="font-bold text-slate-700 text-xs uppercase tracking-wider" />

            <x-text-input id="prodi"
                class="block mt-1 w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-xs"
                type="text"
                name="prodi"
                :value="old('prodi', auth()->user()->prodi)"
                required
                placeholder="Contoh: Teknik Industri" />

            <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
        </div>

        <!-- Jenis Kelamin -->
        <div>
            <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" class="font-bold text-slate-700 text-xs uppercase tracking-wider" />

            <select id="jenis_kelamin"
                name="jenis_kelamin"
                class="block mt-1 w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-xs"
                required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L" {{ old('jenis_kelamin', auth()->user()->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin', auth()->user()->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>

            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
        </div>

        <!-- Nomor Ponsel -->
        <div>
            <x-input-label for="nomor_ponsel" :value="__('Nomor Ponsel (WhatsApp)')" class="font-bold text-slate-700 text-xs uppercase tracking-wider" />

            <x-text-input id="nomor_ponsel"
                class="block mt-1 w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-xs"
                type="text"
                name="nomor_ponsel"
                :value="old('nomor_ponsel', auth()->user()->nomor_ponsel)"
                required
                placeholder="Contoh: 081234567890" />

            <x-input-error :messages="$errors->get('nomor_ponsel')" class="mt-2" />
        </div>

        {{-- Submit Button --}}
        <div class="pt-3">
            <button type="submit"
                    class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-700 text-white font-extrabold text-sm shadow-lg shadow-blue-700/25 hover:from-blue-800 hover:to-indigo-800 hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <span>💾</span>
                <span>Simpan dan Masuk</span>
            </button>
        </div>
    </form>
</x-guest-layout>