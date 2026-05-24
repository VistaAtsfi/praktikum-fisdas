@extends('layouts.koordinator', ['title' => 'Data Asisten'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Data Asisten Praktikum</h1>
        <p class="text-slate-500 mt-2">Buat akun asisten dan kelola status asisten pada periode praktikum.</p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900 mb-5">Tambah Asisten</h2>

        <form method="POST" action="{{ route('koordinator.asisten.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Periode Praktikum</label>
                <select name="praktikum_id" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih Periode</option>
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}">{{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                <select name="status" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                <input type="email" name="email" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Password Awal</label>
                <input type="password" name="password" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" required class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">NIM</label>
                <input type="text" name="nim" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Program Studi</label>
                <input type="text" name="prodi" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Ponsel</label>
                <input type="text" name="nomor_ponsel" class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Buat Akun Asisten
                </button>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-blue-100">
            <form method="GET" action="{{ route('koordinator.asisten') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari asisten"
                       class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

                <select name="praktikum_id" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Periode</option>
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected(request('praktikum_id') == $praktikum->id)>
                            {{ $praktikum->nama }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>

                <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan
                </button>
            </form>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1100px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">Email</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">NIM</th>
                        <th class="p-3 text-center">Prodi</th>
                        <th class="p-3 text-center">Praktikum</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center rounded-r-xl">Reset Password</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($asistens as $asisten)
                        <tr>
                            <td class="p-3 text-center">{{ $asisten->user->email ?? '-' }}</td>
                            <td class="p-3 text-center font-semibold">{{ $asisten->user->name ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $asisten->user->nim ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $asisten->user->prodi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $asisten->praktikum->nama ?? '-' }}</td>
                            <td class="p-3 text-center">{{ ucfirst($asisten->status ?? '-') }}</td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('koordinator.asisten.reset-password', $asisten) }}" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')

                                    <input type="password" name="password" placeholder="Password baru"
                                           class="w-40 rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">

                                    <input type="password" name="password_confirmation" placeholder="Konfirmasi"
                                           class="w-40 rounded-xl border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">

                                    <button type="submit" class="px-3 py-2 rounded-xl bg-blue-700 text-white font-bold text-xs hover:bg-blue-800">
                                        Reset
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-5 text-center text-slate-500">Data asisten belum tersedia.</td>
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
@endsection