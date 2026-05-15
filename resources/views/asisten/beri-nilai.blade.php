@extends('layouts.asisten', ['title' => 'Beri Nilai'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">
                    Beri Nilai
                </h1>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5 text-sm">
                    <p><span class="font-bold">Nama:</span> {{ $praktikan->user->name ?? '-' }}</p>
                    <p><span class="font-bold">NIM:</span> {{ $praktikan->user->nim ?? '-' }}</p>
                    <p><span class="font-bold">Program Studi:</span> {{ $praktikan->user->prodi ?? '-' }}</p>
                    <p><span class="font-bold">Kelompok:</span> {{ $praktikan->kelompok->nama_kelompok ?? '-' }}</p>
                    <p><span class="font-bold">Sesi:</span> {{ $praktikan->kelompok->sesi ?? '-' }}</p>
                    <p><span class="font-bold">Praktikum:</span> {{ $praktikum->nama ?? '-' }}</p>
                </div>
            </div>

            <a href="{{ route('asisten.penilaian') }}"
               class="inline-flex justify-center px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition">
                Kembali
            </a>
        </div>
    </section>

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6">
            <h2 class="font-extrabold text-red-700 mb-3">Terjadi kesalahan input:</h2>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden">
        <div class="p-6 overflow-x-auto">
            <table class="w-full min-w-[1000px] text-sm">
                <thead>
                    <tr class="bg-slate-100">
                        <th class="p-3 text-center rounded-l-xl w-64">Keterangan Penilaian</th>
                        <th class="p-3 text-center">Subjek Penilaian</th>
                        <th class="p-3 text-center rounded-r-xl w-40">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    <tr>
                        <td class="p-4 font-bold text-center">Penulisan Laporan</td>
                        <td class="p-4">
                            <form id="form-penulisan-laporan"
                                  method="POST"
                                  action="{{ route('asisten.penilaian.store', $praktikan) }}">
                                @csrf
                                <input type="hidden" name="kategori" value="penulisan_laporan">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach ($moduls as $modul)
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">
                                                Modul {{ $loop->iteration }}
                                            </label>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   name="laporan_mingguan[{{ $modul->id }}]"
                                                   value="{{ data_get($nilaiPenulisanLaporan?->laporan_mingguan, (string) $modul->id) }}"
                                                   class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    @endforeach

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">
                                            Rata-rata Resmi
                                        </label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               name="rata_rata_resmi"
                                               value="{{ $nilaiPenulisanLaporan->rata_rata_resmi ?? '' }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td class="p-4 text-center">
                            <button type="submit"
                                    form="form-penulisan-laporan"
                                    class="px-5 py-2 rounded-xl border border-slate-300 font-bold hover:bg-slate-50 transition">
                                Simpan
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-center">Laporan Resmi</td>
                        <td class="p-4">
                            <form id="form-laporan-resmi"
                                  method="POST"
                                  action="{{ route('asisten.penilaian.store', $praktikan) }}">
                                @csrf
                                <input type="hidden" name="kategori" value="laporan_resmi">

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    @foreach ($moduls as $modul)
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">
                                                Modul {{ $loop->iteration }}
                                            </label>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   name="nilai_modul[{{ $modul->id }}]"
                                                   value="{{ data_get($nilaiLaporanResmi?->nilai_modul, (string) $modul->id) }}"
                                                   class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        </td>
                        <td class="p-4 text-center">
                            <button type="submit"
                                    form="form-laporan-resmi"
                                    class="px-5 py-2 rounded-xl border border-slate-300 font-bold hover:bg-slate-50 transition">
                                Simpan
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-center">Kemampuan Praktikum</td>
                        <td class="p-4">
                            <form id="form-kemampuan-praktik"
                                  method="POST"
                                  action="{{ route('asisten.penilaian.store', $praktikan) }}">
                                @csrf
                                <input type="hidden" name="kategori" value="kemampuan_praktik">

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    @foreach ($moduls as $modul)
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">
                                                Modul {{ $loop->iteration }}
                                            </label>
                                            <input type="number"
                                                   step="0.01"
                                                   min="0"
                                                   max="100"
                                                   name="nilai_modul[{{ $modul->id }}]"
                                                   value="{{ data_get($nilaiKemampuanPraktikum?->nilai_modul, (string) $modul->id) }}"
                                                   class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    @endforeach
                                </div>
                            </form>
                        </td>
                        <td class="p-4 text-center">
                            <button type="submit"
                                    form="form-kemampuan-praktik"
                                    class="px-5 py-2 rounded-xl border border-slate-300 font-bold hover:bg-slate-50 transition">
                                Simpan
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-center">Nilai Harian</td>
                        <td class="p-4">
                            <form id="form-nilai-harian"
                                  method="POST"
                                  action="{{ route('asisten.penilaian.store', $praktikan) }}">
                                @csrf
                                <input type="hidden" name="kategori" value="nilai_harian">

                                <div class="space-y-5">
                                    <div>
                                        <h4 class="font-extrabold text-slate-800 mb-3">Kehadiran</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            @foreach ($moduls as $modul)
                                                @php
                                                    $statusKehadiran = data_get($nilaiHarian?->kehadiran, (string) $modul->id);
                                                @endphp

                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 mb-1">
                                                        Modul {{ $loop->iteration }}
                                                    </label>

                                                    <select name="kehadiran[{{ $modul->id }}]"
                                                            class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                                        <option value="">-</option>
                                                        <option value="hadir" @selected($statusKehadiran === 'hadir')>Hadir</option>
                                                        <option value="sakit" @selected($statusKehadiran === 'sakit')>Sakit</option>
                                                        <option value="izin" @selected($statusKehadiran === 'izin')>Izin</option>
                                                        <option value="alpa" @selected($statusKehadiran === 'alpa')>Alpa</option>
                                                    </select>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="font-extrabold text-slate-800 mb-3">Responsi</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            @foreach ($moduls as $modul)
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 mb-1">
                                                        Modul {{ $loop->iteration }}
                                                    </label>
                                                    <input type="number"
                                                           step="0.01"
                                                           min="0"
                                                           max="100"
                                                           name="responsi[{{ $modul->id }}]"
                                                           value="{{ data_get($nilaiHarian?->responsi, (string) $modul->id) }}"
                                                           class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="font-extrabold text-slate-800 mb-3">Asistensi</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            @foreach ($moduls as $modul)
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 mb-1">
                                                        Modul {{ $loop->iteration }}
                                                    </label>
                                                    <input type="number"
                                                           step="0.01"
                                                           min="0"
                                                           max="100"
                                                           name="asistensi[{{ $modul->id }}]"
                                                           value="{{ data_get($nilaiHarian?->asistensi, (string) $modul->id) }}"
                                                           class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="font-extrabold text-slate-800 mb-3">Kemampuan Praktik Harian</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            @foreach ($moduls as $modul)
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 mb-1">
                                                        Modul {{ $loop->iteration }}
                                                    </label>
                                                    <input type="number"
                                                           step="0.01"
                                                           min="0"
                                                           max="100"
                                                           name="kemampuan_praktik[{{ $modul->id }}]"
                                                           value="{{ data_get($nilaiHarian?->kemampuan_praktik, (string) $modul->id) }}"
                                                           class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-1">
                                            Nilai Laporan Harian
                                        </label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               name="laporan"
                                               value="{{ $nilaiHarian->laporan ?? '' }}"
                                               class="w-full md:w-64 rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td class="p-4 text-center">
                            <button type="submit"
                                    form="form-nilai-harian"
                                    class="px-5 py-2 rounded-xl border border-slate-300 font-bold hover:bg-slate-50 transition">
                                Simpan
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-center">Nilai Akhir</td>
                        <td class="p-4">
                            <form id="form-nilai-akhir"
                                  method="POST"
                                  action="{{ route('asisten.penilaian.store', $praktikan) }}">
                                @csrf
                                <input type="hidden" name="kategori" value="nilai_akhir">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Kehadiran</label>
                                        <input type="number" step="0.01" min="0" max="100" name="kehadiran"
                                               value="{{ $nilaiAkhir->kehadiran ?? $nilaiHarian->rata_rata_kehadiran ?? '' }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Laporan</label>
                                        <input type="number" step="0.01" min="0" max="100" name="laporan"
                                               value="{{ $nilaiAkhir->laporan ?? $nilaiHarian->laporan ?? '' }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Responsi</label>
                                        <input type="number" step="0.01" min="0" max="100" name="responsi"
                                               value="{{ $nilaiAkhir->responsi ?? $nilaiHarian->rata_rata_responsi ?? '' }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Asistensi</label>
                                        <input type="number" step="0.01" min="0" max="100" name="asistensi"
                                               value="{{ $nilaiAkhir->asistensi ?? $nilaiHarian->rata_rata_asistensi ?? '' }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Kemampuan Praktik</label>
                                        <input type="number" step="0.01" min="0" max="100" name="kemampuan_praktik"
                                               value="{{ $nilaiAkhir->kemampuan_praktik ?? $nilaiHarian->rata_rata_kemampuan_praktik ?? '' }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Nilai Angka</label>
                                        <input type="number" step="0.01" min="0" max="100" name="nilai_angka"
                                               value="{{ $nilaiAkhir->nilai_angka ?? '' }}"
                                               class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td class="p-4 text-center">
                            <button type="submit"
                                    form="form-nilai-akhir"
                                    class="px-5 py-2 rounded-xl border border-slate-300 font-bold hover:bg-slate-50 transition">
                                Simpan
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </section>

</div>
@endsection