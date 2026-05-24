@extends('layouts.koordinator', ['title' => 'Presensi Praktikan'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Presensi Praktikan</h1>
        <p class="text-slate-500 mt-2">Lihat data presensi hasil scan QR maupun input manual oleh asisten.</p>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <form method="GET" action="{{ route('koordinator.presensi') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari NIM, nama, email, kelompok"
                   class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">

            <select name="praktikum_id" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                @foreach ($praktikums as $praktikum)
                    <option value="{{ $praktikum->id }}" @selected($praktikumId == $praktikum->id)>
                        {{ $praktikum->nama }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="rounded-2xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="hadir" @selected(request('status') === 'hadir')>Hadir</option>
                <option value="sakit" @selected(request('status') === 'sakit')>Sakit</option>
                <option value="izin" @selected(request('status') === 'izin')>Izin</option>
                <option value="alpa" @selected(request('status') === 'alpa')>Alpa</option>
            </select>

            <button type="submit" class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                Terapkan
            </button>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1200px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl">Timestamp</th>
                        <th class="p-3 text-center">Asisten</th>
                        <th class="p-3 text-center">NIM</th>
                        <th class="p-3 text-center">Nama</th>
                        <th class="p-3 text-center">Kelompok</th>
                        <th class="p-3 text-center">Sesi</th>
                        <th class="p-3 text-center">Modul</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center rounded-r-xl">Keterangan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($presensis as $presensi)
                        <tr>
                            <td class="p-3 text-center whitespace-nowrap">
                                {{ $presensi->waktu_absen ? $presensi->waktu_absen->format('d/m/Y H:i:s') : '-' }}
                            </td>
                            <td class="p-3 text-center">{{ $presensi->asisten->user->email ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $presensi->nim ?? '-' }}</td>
                            <td class="p-3 text-center font-semibold">{{ $presensi->nama ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $presensi->kelompok ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $presensi->sesi ?? '-' }}</td>
                            <td class="p-3 text-center">{{ $presensi->modul->nama_modul ?? '-' }}</td>
                            <td class="p-3 text-center">
                                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-bold text-xs">
                                    {{ ucfirst($presensi->status ?? '-') }}
                                </span>
                            </td>
                            <td class="p-3 text-center">{{ $presensi->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-5 text-center text-slate-500">Data presensi belum tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $presensis->links() }}
            </div>
        </div>
    </section>

</div>
@endsection