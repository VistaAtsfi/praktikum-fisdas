@extends('layouts.koordinator', ['title' => 'Kategori Penilaian'])

@section('content')
@php
    $kategoriPenilaians = collect($kategoriPenilaians ?? []);
    $kategoriAktif = collect($kategoriAktif ?? $kategoriPenilaians->where('is_aktif', true)->values());

    $formatPersentase = function ($value) {
        $number = number_format((float) $value, 2, '.', '');
        return rtrim(rtrim($number, '0'), '.');
    };

    $styleKategori = function ($tipe) {
        return match ($tipe) {
            'afektif' => [
                'card' => 'border-blue-100 bg-blue-50',
                'label' => 'text-blue-700',
            ],
            'kognitif' => [
                'card' => 'border-purple-100 bg-purple-50',
                'label' => 'text-purple-700',
            ],
            'psikomotorik' => [
                'card' => 'border-cyan-100 bg-cyan-50',
                'label' => 'text-cyan-700',
            ],
            default => [
                'card' => 'border-slate-100 bg-slate-50',
                'label' => 'text-slate-700',
            ],
        };
    };

    $rumusNilai = $kategoriAktif
        ->map(fn ($item) => $item->nama_subjek . ' × ' . $formatPersentase($item->persentase) . '%')
        ->implode(' + ');

    $totalPersentase = $totalPersentase ?? $kategoriAktif->sum(fn ($item) => (float) $item->persentase);
@endphp

<div class="space-y-8 max-w-full overflow-x-hidden"
     x-data="kategoriPenilaianPage()">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Kategori Penilaian
        </h1>

        <p class="text-slate-500 mt-2">
            Komponen penilaian yang digunakan dalam perhitungan nilai akhir praktikan.
        </p>
    </section>

    @if (session('status'))
        <section class="rounded-3xl bg-green-50 border border-green-100 p-5 text-green-700 font-bold">
            {{ session('status') }}
        </section>
    @endif

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

    {{-- Tampilan ringkasan tetap seperti sebelumnya --}}
    <section class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @forelse ($kategoriAktif as $kategori)
            @php
                $style = $styleKategori($kategori->tipe_penilaian);
            @endphp

            <div class="rounded-3xl border {{ $style['card'] }} p-6">
                <p class="text-xs font-extrabold {{ $style['label'] }} uppercase">
                    {{ strtoupper($kategori->tipe_penilaian) }}
                </p>

                <h3 class="font-extrabold text-slate-900 mt-2">
                    {{ $kategori->nama_subjek }}
                </h3>

                <p class="text-3xl font-extrabold text-slate-900 mt-4">
                    {{ $formatPersentase($kategori->persentase) }}%
                </p>
            </div>
        @empty
            <div class="md:col-span-5 rounded-3xl bg-slate-50 border border-slate-100 p-8 text-center text-slate-500">
                Kategori penilaian belum tersedia.
            </div>
        @endforelse
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900">
            Rumus Nilai Akhir
        </h2>

        <div class="mt-5 rounded-3xl bg-slate-50 border border-slate-100 p-6 text-slate-700 leading-relaxed">
            <p>
                <span class="font-extrabold">Nilai Angka</span>
                =
                {{ $rumusNilai ?: 'Belum ada kategori aktif.' }}.
            </p>

            <p class="mt-3 text-sm {{ (float) $totalPersentase === 100.0 ? 'text-slate-500' : 'text-red-600 font-semibold' }}">
                Total persentase kategori aktif: {{ $formatPersentase($totalPersentase) }}%.
                @if ((float) $totalPersentase !== 100.0)
                    Total sebaiknya 100% agar perhitungan nilai akhir sesuai.
                @else
                    Total sudah sesuai 100%.
                @endif
            </p>
        </div>
    </section>

    {{-- Tambah Subjek --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900">
            Tambah Subjek Penilaian
        </h2>

        <p class="text-slate-500 mt-2">
            Gunakan bagian ini jika ada komponen penilaian baru.
        </p>

        <form id="form-tambah-kategori"
              method="POST"
              action="{{ route('koordinator.penilaian.store') }}"
              class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Tipe Penilaian
                </label>

                <select name="tipe_penilaian"
                        required
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Tipe</option>
                    <option value="afektif">Afektif</option>
                    <option value="kognitif">Kognitif</option>
                    <option value="psikomotorik">Psikomotorik</option>
                </select>

                <p class="text-xs text-slate-500 mt-2">
                    Contoh: Afektif untuk kehadiran dan laporan.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Nama Subjek
                </label>

                <input type="text"
                       name="nama_subjek"
                       required
                       placeholder="Contoh: Kehadiran"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-slate-500 mt-2">
                    Nama komponen yang dinilai.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Persentase
                </label>

                <input type="number"
                       name="persentase"
                       step="0.01"
                       min="0"
                       max="100"
                       required
                       placeholder="10"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-slate-500 mt-2">
                    Bobot terhadap nilai akhir.
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Urutan
                </label>

                <input type="number"
                       name="urutan"
                       min="1"
                       value="{{ $kategoriPenilaians->count() + 1 }}"
                       required
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <p class="text-xs text-slate-500 mt-2">
                    Urutan tampil di halaman.
                </p>
            </div>

            <div class="xl:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Keterangan
                </label>

                <textarea name="keterangan"
                          rows="3"
                          placeholder="Jelaskan sumber atau fungsi nilai ini."
                          class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500"></textarea>

                <label class="mt-3 inline-flex items-center gap-2">
                    <input type="checkbox"
                           name="is_aktif"
                           value="1"
                           checked
                           class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">

                    <span class="text-sm font-semibold text-slate-700">
                        Aktif digunakan dalam rumus nilai akhir
                    </span>
                </label>
            </div>

            <div class="md:col-span-2 xl:col-span-6 flex justify-end">
                <button type="button"
                        @click="openConfirm(
                            'form-tambah-kategori',
                            'Simpan subjek penilaian?',
                            'Data subjek penilaian baru akan disimpan dan bisa digunakan dalam perhitungan nilai akhir.'
                        )"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Tambah Subjek
                </button>
            </div>
        </form>
    </section>

    {{-- Tabel CRUD --}}
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden max-w-full">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Daftar Subjek Penilaian
            </h2>

            <p class="text-slate-500 mt-2">
                Tabel ini digunakan untuk mengedit tipe penilaian, nama subjek, persentase, keterangan, urutan, dan status aktif.
            </p>
        </div>

        <div class="p-6 max-w-full overflow-hidden">

            {{-- Tampilan desktop --}}
            <div class="hidden xl:block">
                <table class="w-full table-fixed text-sm">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700">
                            <th class="w-[140px] p-3 text-center rounded-l-xl">
                                Tipe
                            </th>

                            <th class="w-[170px] p-3 text-center">
                                Nama Subjek
                            </th>

                            <th class="w-[120px] p-3 text-center">
                                Persentase
                            </th>

                            <th class="p-3 text-center">
                                Keterangan
                            </th>

                            <th class="w-[90px] p-3 text-center">
                                Urutan
                            </th>

                            <th class="w-[110px] p-3 text-center">
                                Status
                            </th>

                            <th class="w-[150px] p-3 text-center rounded-r-xl">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($kategoriPenilaians as $kategori)
                            @php
                                $formEditId = 'form-edit-kategori-' . $kategori->id;
                                $formDeleteId = 'form-delete-kategori-' . $kategori->id;
                            @endphp

                            <tr class="hover:bg-blue-50/40 transition align-top">
                                <td class="p-3">
                                    <form id="{{ $formEditId }}"
                                        method="POST"
                                        action="{{ route('koordinator.penilaian.update', $kategori) }}">
                                        @csrf
                                        @method('PATCH')
                                    </form>

                                    <select name="tipe_penilaian"
                                            form="{{ $formEditId }}"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="afektif" @selected($kategori->tipe_penilaian === 'afektif')>
                                            Afektif
                                        </option>

                                        <option value="kognitif" @selected($kategori->tipe_penilaian === 'kognitif')>
                                            Kognitif
                                        </option>

                                        <option value="psikomotorik" @selected($kategori->tipe_penilaian === 'psikomotorik')>
                                            Psikomotorik
                                        </option>
                                    </select>

                                    <p class="text-xs text-slate-500 mt-2">
                                        Kelompok jenis nilai.
                                    </p>
                                </td>

                                <td class="p-3">
                                    <input type="text"
                                        name="nama_subjek"
                                        value="{{ $kategori->nama_subjek }}"
                                        form="{{ $formEditId }}"
                                        required
                                        class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">

                                    <p class="text-xs text-slate-500 mt-2">
                                        Nama komponen nilai.
                                    </p>
                                </td>

                                <td class="p-3">
                                    <input type="number"
                                        name="persentase"
                                        value="{{ $formatPersentase($kategori->persentase) }}"
                                        form="{{ $formEditId }}"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        required
                                        class="w-full rounded-xl border-slate-200 text-sm text-center focus:border-blue-500 focus:ring-blue-500">

                                    <p class="text-xs text-slate-500 mt-2 text-center">
                                        Bobot %
                                    </p>
                                </td>

                                <td class="p-3">
                                    <textarea name="keterangan"
                                            form="{{ $formEditId }}"
                                            rows="3"
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">{{ $kategori->keterangan }}</textarea>

                                    <p class="text-xs text-slate-500 mt-2">
                                        Penjelasan singkat agar koordinator tahu fungsi subjek ini.
                                    </p>
                                </td>

                                <td class="p-3">
                                    <input type="number"
                                        name="urutan"
                                        value="{{ $kategori->urutan }}"
                                        form="{{ $formEditId }}"
                                        min="1"
                                        required
                                        class="w-full rounded-xl border-slate-200 text-sm text-center focus:border-blue-500 focus:ring-blue-500">

                                    <p class="text-xs text-slate-500 mt-2 text-center">
                                        Urutan
                                    </p>
                                </td>

                                <td class="p-3 text-center">
                                    <input type="hidden"
                                        name="is_aktif"
                                        value="0"
                                        form="{{ $formEditId }}">

                                    <label class="inline-flex items-center justify-center gap-2">
                                        <input type="checkbox"
                                            name="is_aktif"
                                            value="1"
                                            form="{{ $formEditId }}"
                                            @checked($kategori->is_aktif)
                                            class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">

                                        <span class="text-sm font-semibold text-slate-700">
                                            Aktif
                                        </span>
                                    </label>

                                    <p class="text-xs text-slate-500 mt-2">
                                        Masuk rumus
                                    </p>
                                </td>

                                <td class="p-3">
                                    <div class="flex flex-col gap-2">
                                        <button type="button"
                                                @click="openConfirm(
                                                    '{{ $formEditId }}',
                                                    'Simpan perubahan?',
                                                    'Perubahan pada subjek {{ $kategori->nama_subjek }} akan disimpan.'
                                                )"
                                                class="px-4 py-2 rounded-xl bg-blue-700 text-white font-bold hover:bg-blue-800 transition">
                                            Simpan
                                        </button>

                                        <form id="{{ $formDeleteId }}"
                                            method="POST"
                                            action="{{ route('koordinator.penilaian.delete', $kategori) }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <button type="button"
                                                @click="openConfirm(
                                                    '{{ $formDeleteId }}',
                                                    'Hapus subjek?',
                                                    'Subjek {{ $kategori->nama_subjek }} akan dihapus dari daftar kategori penilaian.'
                                                )"
                                                class="px-4 py-2 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-5 text-center text-slate-500">
                                    Data kategori penilaian belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tampilan layar kecil: turun ke bawah, tidak geser samping --}}
            <div class="xl:hidden space-y-5">
                @forelse ($kategoriPenilaians as $kategori)
                    @php
                        $formEditId = 'form-edit-kategori-mobile-' . $kategori->id;
                        $formDeleteId = 'form-delete-kategori-mobile-' . $kategori->id;
                        $style = $styleKategori($kategori->tipe_penilaian);
                    @endphp

                    <div class="rounded-3xl border border-blue-100 bg-white p-5 shadow-sm">
                        <form id="{{ $formEditId }}"
                            method="POST"
                            action="{{ route('koordinator.penilaian.update', $kategori) }}"
                            class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-extrabold {{ $style['label'] }} uppercase">
                                        {{ strtoupper($kategori->tipe_penilaian) }}
                                    </p>

                                    <h3 class="text-xl font-extrabold text-slate-900 mt-1">
                                        {{ $kategori->nama_subjek }}
                                    </h3>
                                </div>

                                <span class="shrink-0 px-3 py-1 rounded-full {{ $kategori->is_aktif ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }} text-xs font-bold">
                                    {{ $kategori->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Tipe Penilaian
                                </label>

                                <select name="tipe_penilaian"
                                        class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="afektif" @selected($kategori->tipe_penilaian === 'afektif')>
                                        Afektif
                                    </option>

                                    <option value="kognitif" @selected($kategori->tipe_penilaian === 'kognitif')>
                                        Kognitif
                                    </option>

                                    <option value="psikomotorik" @selected($kategori->tipe_penilaian === 'psikomotorik')>
                                        Psikomotorik
                                    </option>
                                </select>

                                <p class="text-xs text-slate-500 mt-2">
                                    Kelompok jenis nilai.
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Nama Subjek
                                </label>

                                <input type="text"
                                    name="nama_subjek"
                                    value="{{ $kategori->nama_subjek }}"
                                    required
                                    class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">

                                <p class="text-xs text-slate-500 mt-2">
                                    Nama komponen nilai, misalnya Kehadiran atau Laporan.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">
                                        Persentase
                                    </label>

                                    <input type="number"
                                        name="persentase"
                                        value="{{ $formatPersentase($kategori->persentase) }}"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        required
                                        class="w-full rounded-2xl border-slate-200 text-sm text-center focus:border-blue-500 focus:ring-blue-500">

                                    <p class="text-xs text-slate-500 mt-2">
                                        Bobot terhadap nilai akhir.
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">
                                        Urutan
                                    </label>

                                    <input type="number"
                                        name="urutan"
                                        value="{{ $kategori->urutan }}"
                                        min="1"
                                        required
                                        class="w-full rounded-2xl border-slate-200 text-sm text-center focus:border-blue-500 focus:ring-blue-500">

                                    <p class="text-xs text-slate-500 mt-2">
                                        Urutan tampil di halaman.
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Keterangan
                                </label>

                                <textarea name="keterangan"
                                        rows="3"
                                        class="w-full rounded-2xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">{{ $kategori->keterangan }}</textarea>

                                <p class="text-xs text-slate-500 mt-2">
                                    Penjelasan singkat agar koordinator tahu fungsi subjek ini.
                                </p>
                            </div>

                            <input type="hidden" name="is_aktif" value="0">

                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox"
                                    name="is_aktif"
                                    value="1"
                                    @checked($kategori->is_aktif)
                                    class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">

                                <span class="text-sm font-semibold text-slate-700">
                                    Aktif digunakan dalam rumus nilai akhir
                                </span>
                            </label>
                        </form>

                        <form id="{{ $formDeleteId }}"
                            method="POST"
                            action="{{ route('koordinator.penilaian.delete', $kategori) }}">
                            @csrf
                            @method('DELETE')
                        </form>

                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="button"
                                    @click="openConfirm(
                                        '{{ $formEditId }}',
                                        'Simpan perubahan?',
                                        'Perubahan pada subjek {{ $kategori->nama_subjek }} akan disimpan.'
                                    )"
                                    class="px-4 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                                Simpan
                            </button>

                            <button type="button"
                                    @click="openConfirm(
                                        '{{ $formDeleteId }}',
                                        'Hapus subjek?',
                                        'Subjek {{ $kategori->nama_subjek }} akan dihapus dari daftar kategori penilaian.'
                                    )"
                                    class="px-4 py-3 rounded-2xl bg-red-600 text-white font-extrabold hover:bg-red-700 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 border border-slate-100 p-6 text-center text-slate-500">
                        Data kategori penilaian belum tersedia.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Modal Konfirmasi --}}
    <div x-cloak
         x-show="confirmOpen"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
        <div @click.outside="confirmOpen = false"
             x-transition
             class="w-full max-w-md rounded-3xl bg-white shadow-2xl border border-blue-100 p-6">
            <h3 class="text-2xl font-extrabold text-slate-900"
                x-text="confirmTitle"></h3>

            <p class="text-slate-500 mt-3 leading-relaxed"
               x-text="confirmMessage"></p>

            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
                <button type="button"
                        @click="confirmOpen = false"
                        class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Batal
                </button>

                <button type="button"
                        @click="submitConfirm()"
                        class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function kategoriPenilaianPage() {
        return {
            confirmOpen: false,
            confirmTitle: '',
            confirmMessage: '',
            formId: null,

            openConfirm(formId, title, message) {
                this.formId = formId;
                this.confirmTitle = title;
                this.confirmMessage = message;
                this.confirmOpen = true;
            },

            submitConfirm() {
                if (!this.formId) {
                    return;
                }

                const form = document.getElementById(this.formId);

                if (form) {
                    form.submit();
                }
            }
        }
    }
</script>
@endsection