@extends('layouts.koordinator', ['title' => 'Data Asisten'])

@section('content')
<div class="space-y-8" x-data="asistenKoordinatorForm()">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Data Asisten Praktikum
        </h1>

        <p class="text-slate-500 mt-2">
            Buat akun asisten dan kelola status asisten pada periode praktikum.
        </p>
    </section>

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3">
                Terjadi kesalahan input:
            </h2>

            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if (session('status'))
        <section class="bg-green-50 border border-green-100 rounded-3xl p-6">
            <p class="font-bold text-green-700">
                {{ session('status') }}
            </p>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">
            Tambah Asisten
        </h2>

        <form method="POST" action="{{ route('koordinator.asisten.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Periode Praktikum
                </label>

                <select name="praktikum_id"
                        required
                        x-model="selectedPraktikum"
                        @change="selectedProdi = ''"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Periode</option>

                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}">
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }} {{ $praktikum->semester }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Status
                </label>

                <select name="status"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="aktif" @selected(old('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(old('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Email
                </label>

                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nama Lengkap
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Password Awal
                </label>

                <div class="relative">
                    <input :type="showPasswordAwal ? 'text' : 'password'"
                        name="password"
                        required
                        class="w-full rounded-2xl border-slate-200 pr-12 focus:border-blue-500 focus:ring-blue-500">

                    <button type="button"
                            @click="showPasswordAwal = !showPasswordAwal"
                            class="absolute inset-y-0 right-4 flex items-center text-slate-500 hover:text-blue-700 transition">

                        {{-- Icon mata --}}
                        <svg x-show="!showPasswordAwal"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        {{-- Icon mata dicoret --}}
                        <svg x-show="showPasswordAwal"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3l18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.88 5.43A10.4 10.4 0 0112 5.25c6 0 9.75 6.75 9.75 6.75a16.1 16.1 0 01-3.18 3.91" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.61 6.61C3.82 8.5 2.25 12 2.25 12s3.75 6.75 9.75 6.75c1.43 0 2.76-.38 3.96-1" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Konfirmasi Password
                </label>

                <div class="relative">
                    <input :type="showPasswordKonfirmasi ? 'text' : 'password'"
                        name="password_confirmation"
                        required
                        class="w-full rounded-2xl border-slate-200 pr-12 focus:border-blue-500 focus:ring-blue-500">

                    <button type="button"
                            @click="showPasswordKonfirmasi = !showPasswordKonfirmasi"
                            class="absolute inset-y-0 right-4 flex items-center text-slate-500 hover:text-blue-700 transition">

                        <svg x-show="!showPasswordKonfirmasi"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        <svg x-show="showPasswordKonfirmasi"
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3l18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.88 5.43A10.4 10.4 0 0112 5.25c6 0 9.75 6.75 9.75 6.75a16.1 16.1 0 01-3.18 3.91" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.61 6.61C3.82 8.5 2.25 12 2.25 12s3.75 6.75 9.75 6.75c1.43 0 2.76-.38 3.96-1" />
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    NIM
                </label>

                <input type="text"
                       name="nim"
                       value="{{ old('nim') }}"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>

                <select name="prodi"
                        required
                        x-model="selectedProdi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Program Studi</option>

                    <template x-for="prodi in prodiOptions" :key="prodi">
                        <option :value="prodi" x-text="prodi"></option>
                    </template>
                </select>

                <p x-show="selectedPraktikum && prodiOptions.length === 0"
                   class="text-xs text-red-500 mt-2">
                    Program studi untuk periode ini belum ditambahkan di menu Periode Praktikum.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jenis Kelamin
                </label>

                <select name="jenis_kelamin"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nomor Ponsel
                </label>

                <input type="text"
                       name="nomor_ponsel"
                       value="{{ old('nomor_ponsel') }}"
                       placeholder="Contoh: 081234567890"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit"
                        class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Buat Akun Asisten
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <form method="GET" action="{{ route('koordinator.asisten') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari asisten"
                       class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <select name="praktikum_id"
                        class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Periode</option>

                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected(request('praktikum_id') == $praktikum->id)>
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }}
                        </option>
                    @endforeach
                </select>

                <select name="status"
                        class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>

                <div class="flex gap-3">
                    <button type="submit"
                            class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                        Cari
                    </button>

                    <a href="{{ route('koordinator.asisten') }}"
                       class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1600px] text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-800">
                        <th class="p-3 text-center rounded-l-xl">Email</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">NIM</th>
                        <th class="p-3 text-center">Prodi</th>
                        <th class="p-3 text-center">Jenis Kelamin</th>
                        <th class="p-3 text-center">No. Ponsel</th>
                        <th class="p-3 text-center">Praktikum</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center rounded-r-xl">Reset Password</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($asistens as $asisten)
                        <tr class="hover:bg-blue-50/40 transition">
                            <td class="p-3 text-center whitespace-nowrap">
                                {{ $asisten->user->email ?? '-' }}
                            </td>

                            <td class="p-3 text-center font-semibold">
                                {{ $asisten->user->name ?? '-' }}
                            </td>

                            <td class="p-3 text-center whitespace-nowrap">
                                {{ $asisten->user->nim ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $asisten->user->prodi ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ match ($asisten->user->jenis_kelamin ?? null) {
                                    'L' => 'Laki-laki',
                                    'P' => 'Perempuan',
                                    default => '-',
                                } }}
                            </td>

                            <td class="p-3 text-center whitespace-nowrap">
                                {{ $asisten->user->nomor_ponsel ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                {{ $asisten->praktikum->nama ?? '-' }}
                            </td>

                            <td class="p-3 text-center">
                                @if (($asisten->status ?? null) === 'aktif')
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-bold text-xs">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 font-bold text-xs">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="p-3">
                                <form method="POST"
                                      action="{{ route('koordinator.asisten.reset-password', $asisten) }}"
                                      x-data="{ showPassword: false, showConfirmation: false }"
                                      class="grid grid-cols-[180px_180px_auto] gap-2 items-center">
                                    @csrf
                                    @method('PATCH')

                                    <div class="relative">
                                        <input :type="showPassword ? 'text' : 'password'"
                                            name="password"
                                            placeholder="Password baru"
                                            class="w-full rounded-xl border-slate-200 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">

                                        <button type="button"
                                                @click="showPassword = !showPassword"
                                                class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-blue-700 transition">
                                            <svg x-show="!showPassword"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>

                                            <svg x-show="showPassword"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 3l18 18" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9.88 5.43A10.4 10.4 0 0112 5.25c6 0 9.75 6.75 9.75 6.75a16.1 16.1 0 01-3.18 3.91" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.61 6.61C3.82 8.5 2.25 12 2.25 12s3.75 6.75 9.75 6.75c1.43 0 2.76-.38 3.96-1" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input :type="showConfirmation ? 'text' : 'password'"
                                            name="password_confirmation"
                                            placeholder="Konfirmasi"
                                            class="w-full rounded-xl border-slate-200 pr-10 text-sm focus:border-blue-500 focus:ring-blue-500">

                                        <button type="button"
                                                @click="showConfirmation = !showConfirmation"
                                                class="absolute inset-y-0 right-3 flex items-center text-slate-500 hover:text-blue-700 transition">
                                            <svg x-show="!showConfirmation"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>

                                            <svg x-show="showConfirmation"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-4 h-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 3l18 18" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10.58 10.58A3 3 0 0012 15a3 3 0 002.42-4.42" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9.88 5.43A10.4 10.4 0 0112 5.25c6 0 9.75 6.75 9.75 6.75a16.1 16.1 0 01-3.18 3.91" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.61 6.61C3.82 8.5 2.25 12 2.25 12s3.75 6.75 9.75 6.75c1.43 0 2.76-.38 3.96-1" />
                                            </svg>
                                        </button>
                                    </div>

                                    <button type="submit"
                                            class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold text-xs hover:bg-blue-800 transition">
                                        Reset
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-5 text-center text-slate-500">
                                Data asisten belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $asistens->links() }}
            </div>
        </div>
    </section>

</div>

<script>
    function asistenKoordinatorForm() {
        return {
            selectedPraktikum: @json((string) old('praktikum_id', '')),
            selectedProdi: @json(old('prodi', '')),
            showPasswordAwal: false,
            showPasswordKonfirmasi: false,
            prodisByPraktikum: @json($prodisByPraktikum ?? []),

            get prodiOptions() {
                return this.prodisByPraktikum[this.selectedPraktikum] ?? [];
            }
        }
    }
</script>
@endsection