<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Lengkapi Data Praktikan</h2>
        <p class="text-sm text-gray-600 mt-1">
            Data ini diperlukan sebelum masuk ke dashboard praktikan.
        </p>
    </div>

    <form method="POST" action="{{ route('profile.complete.update') }}">
        @csrf
        @method('PATCH')

        <!-- Praktikum -->
        <div>
            <x-input-label for="praktikum_id" :value="__('Jenis Praktikum')" />

            <select id="praktikum_id"
                name="praktikum_id"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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
        <div class="mt-4">
            <x-input-label for="name" :value="__('Nama Lengkap')" />

            <x-text-input id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name', auth()->user()->name === 'Pengguna Baru' ? '' : auth()->user()->name)"
                required
                autofocus />

            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- NIM -->
        <div class="mt-4">
            <x-input-label for="nim" :value="__('NIM')" />

            <x-text-input id="nim"
                class="block mt-1 w-full"
                type="text"
                name="nim"
                :value="old('nim', auth()->user()->nim)"
                required />

            <x-input-error :messages="$errors->get('nim')" class="mt-2" />
        </div>

        <!-- Prodi -->
        <div class="mt-4">
            <x-input-label for="prodi" :value="__('Program Studi')" />

            <x-text-input id="prodi"
                class="block mt-1 w-full"
                type="text"
                name="prodi"
                :value="old('prodi', auth()->user()->prodi)"
                required />

            <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
        </div>

        <!-- Jenis Kelamin -->
        <div class="mt-4">
            <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />

            <select id="jenis_kelamin"
                name="jenis_kelamin"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="L" {{ old('jenis_kelamin', auth()->user()->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin', auth()->user()->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>

            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
        </div>

        <!-- Nomor Ponsel -->
        <div class="mt-4">
            <x-input-label for="nomor_ponsel" :value="__('Nomor Ponsel')" />

            <x-text-input id="nomor_ponsel"
                class="block mt-1 w-full"
                type="text"
                name="nomor_ponsel"
                :value="old('nomor_ponsel', auth()->user()->nomor_ponsel)"
                required />

            <x-input-error :messages="$errors->get('nomor_ponsel')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
            </form>

            <x-primary-button>
                Simpan dan Masuk
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>