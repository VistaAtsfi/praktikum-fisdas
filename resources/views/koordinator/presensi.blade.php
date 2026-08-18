@extends('layouts.koordinator', ['title' => 'Presensi Praktikan'])

@section('content')
<div class="space-y-8 max-w-full overflow-x-hidden">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">
            Presensi Praktikan
        </h1>

        <p class="text-slate-500 mt-2">
            Lihat data presensi hasil scan QR maupun input manual oleh asisten.
        </p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('koordinator.presensi') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Cari Presensi
                </label>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari NIM, nama, email, kelompok"
                       class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Periode Praktikum
                </label>

                <select name="praktikum_id"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($praktikums as $praktikum)
                        <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                            {{ $praktikum->nama }} - {{ $praktikum->tahun_akademik }} {{ $praktikum->semester }}
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

                    @foreach (($prodis ?? collect()) as $prodi)
                        <option value="{{ $prodi }}" @selected(request('prodi') === $prodi)>
                            {{ $prodi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Modul
                </label>

                <select name="modul_id"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Modul</option>

                    @foreach (($moduls ?? collect()) as $modul)
                        <option value="{{ $modul->id }}" @selected(request('modul_id') == $modul->id)>
                            Modul {{ $loop->iteration }} - {{ $modul->nama_modul }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Status Kehadiran
                </label>

                <select name="status"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="hadir" @selected(request('status') === 'hadir')>Hadir</option>
                    <option value="sakit" @selected(request('status') === 'sakit')>Sakit</option>
                    <option value="izin" @selected(request('status') === 'izin')>Izin</option>
                    <option value="alpa" @selected(request('status') === 'alpa')>Alpa</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Kelompok
                </label>

                <select name="kelompok"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Kelompok</option>

                    @foreach (($kelompoks ?? collect()) as $kelompok)
                        <option value="{{ $kelompok->nama_kelompok }}"
                                @selected(request('kelompok') === $kelompok->nama_kelompok)>
                            {{ $kelompok->nama_kelompok }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Sesi Praktikum
                </label>

                <select name="sesi"
                        class="w-full rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Semua Sesi</option>

                    @foreach (($sesis ?? collect()) as $sesi)
                        <option value="{{ $sesi }}" @selected(request('sesi') === $sesi)>
                            {{ $sesi }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                        class="w-full px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Terapkan
                </button>

                <a href="{{ route('koordinator.presensi') }}"
                   class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden max-w-full">
        <div class="p-6 max-w-full overflow-hidden">

            <table class="w-full table-fixed text-[11px]">
                <thead>
                    <tr class="bg-slate-100 text-slate-700">
                        <th class="w-[10%] p-2 text-center rounded-l-xl">
                            Timestamp
                        </th>

                        <th class="w-[13%] p-2 text-center">
                            Modul
                        </th>

                        <th class="w-[13%] p-2 text-center">
                            Asisten
                        </th>

                        <th class="w-[9%] p-2 text-center">
                            NIM
                        </th>

                        <th class="w-[11%] p-2 text-center">
                            Nama
                        </th>

                        <th class="w-[10%] p-2 text-center">
                            Prodi
                        </th>

                        <th class="w-[8%] p-2 text-center">
                            Kelompok
                        </th>

                        <th class="w-[7%] p-2 text-center">
                            Sesi
                        </th>

                        <th class="w-[7%] p-2 text-center">
                            Status
                        </th>

                        <th class="w-[12%] p-2 text-center rounded-r-xl">
                            Keterangan
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($presensis as $presensi)
                        @php
                            $status = strtolower($presensi->status ?? '');

                            $statusClass = match ($status) {
                                'hadir' => 'bg-green-100 text-green-700',
                                'sakit', 'izin', 'alpa' => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-500',
                            };

                            $nomorModul = ($moduls ?? collect())
                                ->pluck('id')
                                ->search($presensi->modul_id);
                        @endphp

                        <tr class="hover:bg-blue-50/40 transition align-top">

                            {{-- Timestamp --}}
                            <td class="p-2 text-center break-words">
                                {{ $presensi->waktu_absen
                                    ? $presensi->waktu_absen->format('d/m/Y H:i:s')
                                    : '-' }}
                            </td>

                            {{-- Modul --}}
                            <td class="p-2 text-center break-words">
                                @if ($nomorModul !== false)
                                    <div class="font-bold text-slate-800">
                                        Modul {{ $nomorModul + 1 }}
                                    </div>

                                    <div class="text-[10px] leading-relaxed text-slate-500 mt-1 break-words">
                                        {{ $presensi->modul->nama_modul ?? '-' }}
                                    </div>
                                @else
                                    <span class="break-words">
                                        {{ $presensi->modul->nama_modul ?? '-' }}
                                    </span>
                                @endif
                            </td>

                            {{-- Asisten --}}
                            <td class="p-2 text-center break-all">
                                {{ $presensi->asisten->user->email ?? '-' }}
                            </td>

                            {{-- NIM --}}
                            <td class="p-2 text-center font-bold break-words">
                                {{ $presensi->nim
                                    ?? $presensi->praktikan->user->nim
                                    ?? '-' }}
                            </td>

                            {{-- Nama --}}
                            <td class="p-2 text-center font-semibold break-words">
                                {{ $presensi->nama
                                    ?? $presensi->praktikan->user->name
                                    ?? '-' }}
                            </td>

                            {{-- Prodi --}}
                            <td class="p-2 text-center break-words">
                                {{ $presensi->praktikan->user->prodi ?? '-' }}
                            </td>

                            {{-- Kelompok --}}
                            <td class="p-2 text-center break-words">
                                {{ $presensi->kelompok
                                    ?? $presensi->praktikan->kelompok->nama_kelompok
                                    ?? '-' }}
                            </td>

                            {{-- Sesi --}}
                            <td class="p-2 text-center break-words">
                                {{ $presensi->sesi
                                    ?? $presensi->praktikan->kelompok->sesi
                                    ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="p-2 text-center">
                                <span class="inline-flex justify-center px-2 py-1 rounded-full font-bold text-[10px] {{ $statusClass }}">
                                    {{ $presensi->status
                                        ? ucfirst($presensi->status)
                                        : '-' }}
                                </span>
                            </td>

                            {{-- Keterangan --}}
                            <td class="p-2 text-center break-words">
                                {{ $presensi->keterangan ?? '-' }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="10"
                                class="p-5 text-center text-slate-500">
                                Data presensi belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6 max-w-full">
                {{ $presensis->links() }}
            </div>

        </div>
    </section>

</div>
@endsection