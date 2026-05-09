@extends('layouts.praktikan', ['title' => 'Biodata Diri'])

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Biodata Diri Praktikan
        </h1>

        <p class="text-slate-500 mt-2">
            Praktikan dapat memperbarui biodata pribadi. Email tidak dapat diubah dari halaman ini.
        </p>
    </section>

    @if (session('status') === 'biodata-updated')
        <div class="p-4 rounded-2xl bg-green-100 text-green-700 font-bold">
            Biodata berhasil diperbarui.
        </div>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="POST" action="{{ route('praktikan.biodata.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email"
                    class="block mt-1 w-full rounded-2xl border-slate-200 bg-slate-50"
                    type="email"
                    value="{{ $praktikan->user->email }}"
                    disabled />
            </div>

            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name"
                    class="block mt-1 w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                    type="text"
                    name="name"
                    :value="old('name', $praktikan->user->name)"
                    required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="nim" :value="__('NIM')" />
                <x-text-input id="nim"
                    class="block mt-1 w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                    type="text"
                    name="nim"
                    :value="old('nim', $praktikan->user->nim)"
                    required />
                <x-input-error :messages="$errors->get('nim')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="prodi" :value="__('Program Studi')" />
                <x-text-input id="prodi"
                    class="block mt-1 w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                    type="text"
                    name="prodi"
                    :value="old('prodi', $praktikan->user->prodi)"
                    required />
                <x-input-error :messages="$errors->get('prodi')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />

                <select id="jenis_kelamin"
                    name="jenis_kelamin"
                    class="block mt-1 w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                    required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" {{ old('jenis_kelamin', $praktikan->user->jenis_kelamin) === 'L' ? 'selected' : '' }}>
                        Laki-laki
                    </option>
                    <option value="P" {{ old('jenis_kelamin', $praktikan->user->jenis_kelamin) === 'P' ? 'selected' : '' }}>
                        Perempuan
                    </option>
                </select>

                <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="nomor_ponsel" :value="__('Nomor Ponsel')" />
                <x-text-input id="nomor_ponsel"
                    class="block mt-1 w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                    type="text"
                    name="nomor_ponsel"
                    :value="old('nomor_ponsel', $praktikan->user->nomor_ponsel)"
                    required />
                <x-input-error :messages="$errors->get('nomor_ponsel')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-3">
                <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">
                    <p class="text-sm text-blue-700 font-bold">Praktikum</p>
                    <p class="font-extrabold text-slate-900 mt-2">
                        {{ $praktikan->praktikum->nama ?? '-' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">
                    <p class="text-sm text-blue-700 font-bold">Kelompok</p>
                    <p class="font-extrabold text-slate-900 mt-2">
                        {{ $praktikan->kelompok->nama_kelompok ?? 'Belum ditentukan' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">
                    <p class="text-sm text-blue-700 font-bold">Sesi</p>
                    <p class="font-extrabold text-slate-900 mt-2">
                        {{ $praktikan->kelompok->sesi ?? 'Belum ditentukan' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <a href="{{ route('dashboard.praktikan') }}"
                   class="w-full sm:w-auto px-6 py-3 rounded-2xl border border-slate-300 text-slate-700 text-center font-bold hover:bg-slate-50 transition">
                    Batal
                </a>

                <button type="submit"
                    class="w-full sm:flex-1 px-6 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>
</div>
@endsection