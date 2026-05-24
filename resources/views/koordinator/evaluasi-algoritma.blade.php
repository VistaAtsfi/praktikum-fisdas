@extends('layouts.koordinator', ['title' => 'Evaluasi Algoritma'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Evaluasi Algoritma</h1>
        <p class="text-slate-500 mt-2">
            Lihat hasil pengujian algoritma pencarian dan pengurutan yang dicatat oleh sistem.
        </p>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-8">

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-blue-100">
                <h2 class="text-2xl font-extrabold text-slate-900">
                    Evaluasi Pencarian Data
                </h2>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead>
                        <tr class="bg-slate-100">
                            <th class="p-3 text-center rounded-l-xl">Data</th>
                            <th class="p-3 text-center">Field</th>
                            <th class="p-3 text-center">Kata Kunci</th>
                            <th class="p-3 text-center">Waktu Server</th>
                            <th class="p-3 text-center">Iterasi</th>
                            <th class="p-3 text-center rounded-r-xl">Memori</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logSearches as $log)
                            <tr>
                                <td class="p-3 text-center">{{ $log->jumlah_data }}</td>
                                <td class="p-3 text-center">{{ $log->field_pencarian }}</td>
                                <td class="p-3 text-center">{{ $log->kata_kunci_pencarian }}</td>
                                <td class="p-3 text-center">{{ $log->waktu_pemrosesan_server }} ms</td>
                                <td class="p-3 text-center">{{ $log->jumlah_iterasi }}</td>
                                <td class="p-3 text-center">{{ $log->penggunaan_memori }} B</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-5 text-center text-slate-500">
                                    Log pencarian belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-blue-100">
                <h2 class="text-2xl font-extrabold text-slate-900">
                    Evaluasi Pengurutan Data
                </h2>
            </div>

            <div class="p-6 overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead>
                        <tr class="bg-slate-100">
                            <th class="p-3 text-center rounded-l-xl">Data</th>
                            <th class="p-3 text-center">Field</th>
                            <th class="p-3 text-center">Arah</th>
                            <th class="p-3 text-center">Waktu Server</th>
                            <th class="p-3 text-center">Perbandingan</th>
                            <th class="p-3 text-center rounded-r-xl">Memori</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logSortings as $log)
                            <tr>
                                <td class="p-3 text-center">{{ $log->jumlah_data }}</td>
                                <td class="p-3 text-center">{{ $log->field_pengurutan }}</td>
                                <td class="p-3 text-center">{{ strtoupper($log->arah_pengurutan) }}</td>
                                <td class="p-3 text-center">{{ $log->waktu_pemrosesan_server }} ms</td>
                                <td class="p-3 text-center">{{ $log->jumlah_perbandingan }}</td>
                                <td class="p-3 text-center">{{ $log->penggunaan_memori }} B</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-5 text-center text-slate-500">
                                    Log pengurutan belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </section>

</div>
@endsection