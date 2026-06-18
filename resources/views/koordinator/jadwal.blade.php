@extends('layouts.koordinator', ['title' => 'Jadwal Praktikum'])

@section('content')
<div class="space-y-8 max-w-full overflow-x-hidden"
     x-data="{
        deleteModal: false,
        deleteUrl: '',
        deleteTitle: '',
        openDelete(url, title) {
            this.deleteUrl = url;
            this.deleteTitle = title;
            this.deleteModal = true;
        },
        closeDelete() {
            this.deleteModal = false;
            this.deleteUrl = '';
            this.deleteTitle = '';
        }
     }">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Jadwal Praktikum
        </h1>

        <p class="text-slate-500 mt-2 max-w-3xl">
            Kelola jadwal pengambilan/pengumpulan laporan serta jadwal modul dan sesi praktikum.
            Jam dapat diatur menggunakan format mulai sampai selesai.
        </p>
    </section>

    @if (session('status'))
        <section class="bg-green-50 border border-green-100 rounded-3xl p-5 text-green-700 font-bold">
            {{ session('status') }}
        </section>
    @endif

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3">
                Terjadi kesalahan:
            </h2>

            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET"
              action="{{ route('koordinator.jadwal') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Periode Praktikum
                </label>

                <select name="praktikum_id"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }} {{ ucfirst($praktikum->semester) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>

                <select name="prodi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Prodi</option>

                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected($selectedProdi === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Tampilkan
                </button>

                <a href="{{ route('koordinator.jadwal') }}"
                   class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900">
            Tambah Jadwal Pengambilan/Pengumpulan Laporan
        </h2>

        <p class="text-slate-500 mt-2">
            Gunakan bagian ini untuk mengatur hari dan jam laporan per program studi.
        </p>

        <form method="POST"
              action="{{ route('koordinator.jadwal.laporan.store') }}"
              class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-6">
            @csrf

            <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>

                <select name="prodi"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Prodi</option>

                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected(old('prodi') === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Hari
                </label>

                <select name="hari"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                        <option value="{{ $hari }}" @selected(old('hari') === $hari)>
                            {{ $hari }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jam Mulai
                </label>

                <input type="time"
                       name="jam_mulai"
                       value="{{ old('jam_mulai') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jam Selesai
                </label>

                <input type="time"
                       name="jam_selesai"
                       value="{{ old('jam_selesai') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Keterangan
                </label>

                <input type="text"
                       name="keterangan"
                       value="{{ old('keterangan') }}"
                       placeholder="Opsional"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-6 flex justify-end">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Jadwal Laporan
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Jadwal Pengambilan/Pengumpulan Laporan
            </h2>
        </div>

        <div class="max-w-full overflow-x-hidden">
            <table class="w-full table-fixed text-xs">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-3 py-3 text-left w-[18%]">Prodi</th>
                        <th class="px-3 py-3 text-left w-[12%]">Hari</th>
                        <th class="px-3 py-3 text-left w-[14%]">Jam</th>
                        <th class="px-3 py-3 text-left w-[26%]">Keterangan</th>
                        <th class="px-3 py-3 text-right w-[30%]">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-blue-50">
                    @forelse ($jadwalLaporans as $jadwal)
                        <tr>
                            <form method="POST" action="{{ route('koordinator.jadwal.laporan.update', $jadwal) }}">
                                @csrf
                                @method('PATCH')

                                <td class="px-3 py-3">
                                    <select name="prodi"
                                            class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                        @foreach ($prodis as $prodi)
                                            <option value="{{ $prodi }}" @selected($jadwal->prodi === $prodi)>
                                                {{ $prodi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-3 py-3">
                                    <select name="hari"
                                            class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                        @foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                            <option value="{{ $hari }}" @selected($jadwal->hari === $hari)>
                                                {{ $hari }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="px-3 py-3">
                                    <div class="grid grid-cols-1 gap-2">
                                        <input type="time"
                                               name="jam_mulai"
                                               value="{{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '' }}"
                                               class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">

                                        <input type="time"
                                               name="jam_selesai"
                                               value="{{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '' }}"
                                               class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </td>

                                <td class="px-3 py-3">
                                    <input type="text"
                                           name="keterangan"
                                           value="{{ $jadwal->keterangan }}"
                                           class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                </td>

                                <td class="px-3 py-3 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button type="submit"
                                                class="px-3 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                                            Simpan
                                        </button>

                                        <button type="button"
                                                @click="openDelete('{{ route('koordinator.jadwal.laporan.delete', $jadwal) }}', 'jadwal laporan {{ $jadwal->prodi }}')"
                                                class="px-3 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                Jadwal laporan belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900">
            Tambah Jadwal Modul dan Sesi
        </h2>

        <p class="text-slate-500 mt-2">
            Atur minggu, jam praktikum, modul, dan kelompok. Format jam menggunakan jam mulai sampai jam selesai.
        </p>

        <form method="POST"
              action="{{ route('koordinator.jadwal.modul.store') }}"
              class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-6">
            @csrf

            <input type="hidden" name="praktikum_id" value="{{ $praktikumId }}">

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Program Studi
                </label>

                <select name="prodi"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Prodi</option>

                    @foreach ($prodis as $prodi)
                        <option value="{{ $prodi }}" @selected(old('prodi') === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Minggu Ke
                </label>

                <input type="number"
                       name="minggu_ke"
                       value="{{ old('minggu_ke', 1) }}"
                       min="1"
                       max="20"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Hari
                </label>

                <select name="hari"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Hari</option>
                    <option value="Senin" @selected(old('hari') === 'Senin')>Senin</option>
                    <option value="Selasa" @selected(old('hari') === 'Selasa')>Selasa</option>
                    <option value="Rabu" @selected(old('hari') === 'Rabu')>Rabu</option>
                    <option value="Kamis" @selected(old('hari') === 'Kamis')>Kamis</option>
                    <option value="Jumat" @selected(old('hari') === 'Jumat')>Jumat</option>
                    <option value="Sabtu" @selected(old('hari') === 'Sabtu')>Sabtu</option>
                    <option value="Minggu" @selected(old('hari') === 'Minggu')>Minggu</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jam Mulai
                </label>

                <input type="time"
                       name="jam_mulai"
                       value="{{ old('jam_mulai') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Jam Selesai
                </label>

                <input type="time"
                       name="jam_selesai"
                       value="{{ old('jam_selesai') }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Modul
                </label>

                <select name="modul_id"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Modul</option>

                    @foreach ($moduls as $modul)
                        <option value="{{ $modul->id }}">
                            Modul {{ $modul->nomor_modul }} - {{ $modul->nama_modul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Kelompok
                </label>

                <select name="kelompok_id"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Kelompok</option>

                    @foreach ($kelompoks as $kelompok)
                        <option value="{{ $kelompok->id }}">
                            {{ $kelompok->prodi }} - {{ $kelompok->nama_kelompok }} - {{ $kelompok->sesi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Keterangan
                </label>

                <input type="text"
                       name="keterangan"
                       value="{{ old('keterangan') }}"
                       placeholder="Opsional"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-6 flex justify-end">
                <button type="submit"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Simpan Jadwal Modul
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Jadwal Modul dan Sesi Praktikum
            </h2>

            <p class="text-slate-500 mt-2">
                Data dikelompokkan berdasarkan program studi dan minggu praktikum.
            </p>
        </div>

        <div class="p-5 space-y-6 max-w-full overflow-x-hidden">
            @forelse ($jadwalModulByProdi as $prodi => $mingguRows)
                <div class="rounded-3xl border border-blue-100 overflow-hidden">
                    <div class="px-5 py-4 bg-blue-50 border-b border-blue-100">
                        <h3 class="text-xl font-black text-slate-900">
                            {{ $prodi }}
                        </h3>
                    </div>

                    @foreach ($mingguRows as $mingguKe => $rows)
                        <div class="border-b border-blue-50 last:border-b-0">
                            <div class="px-5 py-3 bg-slate-50">
                                <h4 class="font-extrabold text-slate-800">
                                    Minggu {{ $mingguKe }}
                                </h4>
                            </div>

                            <table class="w-full table-fixed text-xs">
                                <thead class="bg-slate-100 text-slate-700">
                                    <tr>
                                        <th class="px-3 py-3 text-left w-[14%]">Jam</th>
                                        <th class="px-3 py-3 text-left w-[20%]">Modul</th>
                                        <th class="px-3 py-3 text-left w-[18%]">Kelompok</th>
                                        <th class="px-3 py-3 text-left w-[20%]">Keterangan</th>
                                        <th class="px-3 py-3 text-right w-[28%]">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-blue-50">
                                    @foreach ($rows as $jadwal)
                                        <tr>
                                            <form method="POST" action="{{ route('koordinator.jadwal.modul.update', $jadwal) }}">
                                                @csrf
                                                @method('PATCH')

                                                <input type="hidden" name="prodi" value="{{ $jadwal->prodi }}">

                                                <td class="px-3 py-3">
                                                    <input type="hidden" name="minggu_ke" value="{{ $jadwal->minggu_ke }}">

                                                    <div class="grid grid-cols-1 gap-2">
                                                        <input type="time"
                                                               name="jam_mulai"
                                                               value="{{ $jadwal->jam_mulai ? substr($jadwal->jam_mulai, 0, 5) : '' }}"
                                                               class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">

                                                        <input type="time"
                                                               name="jam_selesai"
                                                               value="{{ $jadwal->jam_selesai ? substr($jadwal->jam_selesai, 0, 5) : '' }}"
                                                               class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                                    </div>
                                                </td>

                                                <td class="px-3 py-3">
                                                    <select name="modul_id"
                                                            class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                                        @foreach ($moduls as $modul)
                                                            <option value="{{ $modul->id }}" @selected($jadwal->modul_id == $modul->id)>
                                                                Modul {{ $modul->nomor_modul }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td class="px-3 py-3">
                                                    <select name="kelompok_id"
                                                            class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                                        @foreach ($kelompoks->where('prodi', $jadwal->prodi) as $kelompok)
                                                            <option value="{{ $kelompok->id }}" @selected($jadwal->kelompok_id == $kelompok->id)>
                                                                {{ $kelompok->nama_kelompok }} - {{ $kelompok->sesi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td class="px-3 py-3">
                                                    <input type="text"
                                                           name="keterangan"
                                                           value="{{ $jadwal->keterangan }}"
                                                           class="w-full rounded-xl border-slate-200 text-xs focus:border-blue-500 focus:ring-blue-500">
                                                </td>

                                                <td class="px-3 py-3 text-right">
                                                    <div class="flex flex-wrap justify-end gap-2">
                                                        <button type="submit"
                                                                class="px-3 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                                                            Simpan
                                                        </button>

                                                        <button type="button"
                                                                @click="openDelete('{{ route('koordinator.jadwal.modul.delete', $jadwal) }}', 'jadwal {{ $jadwal->prodi }} minggu {{ $jadwal->minggu_ke }}')"
                                                                class="px-3 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition">
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </form>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="rounded-3xl border border-blue-100 p-8 text-center text-slate-500">
                    Jadwal modul dan sesi belum tersedia.
                </div>
            @endforelse
        </div>
    </section>

    <div x-cloak
         x-show="deleteModal"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
        <div @click.outside="closeDelete()"
             x-transition
             class="w-full max-w-md bg-white rounded-3xl shadow-2xl border border-red-100 overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-3xl mb-4">
                    ⚠️
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900">
                    Hapus Jadwal?
                </h2>

                <p class="text-slate-500 mt-3 leading-relaxed">
                    Data <span class="font-bold text-slate-800" x-text="deleteTitle"></span>
                    akan dihapus.
                </p>

                <p class="text-sm text-red-500 mt-2">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="px-6 pb-6 grid grid-cols-2 gap-3">
                <button type="button"
                        @click="closeDelete()"
                        class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Batal
                </button>

                <form method="POST" x-bind:action="deleteUrl">
                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="w-full px-5 py-3 rounded-2xl bg-red-600 text-white font-extrabold hover:bg-red-700 transition">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection