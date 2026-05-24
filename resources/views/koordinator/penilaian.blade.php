@extends('layouts.koordinator', ['title' => 'Kategori Penilaian'])

@section('content')
<div class="space-y-8">

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h1 class="text-3xl font-extrabold text-slate-900">Kategori Penilaian</h1>
        <p class="text-slate-500 mt-2">
            Komponen penilaian yang digunakan dalam perhitungan nilai akhir praktikan.
        </p>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="rounded-3xl border border-blue-100 bg-blue-50 p-6">
            <p class="text-xs font-extrabold text-blue-700 uppercase">Afektif</p>
            <h3 class="font-extrabold text-slate-900 mt-2">Kehadiran</h3>
            <p class="text-3xl font-extrabold text-slate-900 mt-4">10%</p>
        </div>

        <div class="rounded-3xl border border-blue-100 bg-blue-50 p-6">
            <p class="text-xs font-extrabold text-blue-700 uppercase">Afektif</p>
            <h3 class="font-extrabold text-slate-900 mt-2">Laporan</h3>
            <p class="text-3xl font-extrabold text-slate-900 mt-4">10%</p>
        </div>

        <div class="rounded-3xl border border-purple-100 bg-purple-50 p-6">
            <p class="text-xs font-extrabold text-purple-700 uppercase">Kognitif</p>
            <h3 class="font-extrabold text-slate-900 mt-2">Responsi</h3>
            <p class="text-3xl font-extrabold text-slate-900 mt-4">10%</p>
        </div>

        <div class="rounded-3xl border border-purple-100 bg-purple-50 p-6">
            <p class="text-xs font-extrabold text-purple-700 uppercase">Kognitif</p>
            <h3 class="font-extrabold text-slate-900 mt-2">Asistensi</h3>
            <p class="text-3xl font-extrabold text-slate-900 mt-4">20%</p>
        </div>

        <div class="rounded-3xl border border-cyan-100 bg-cyan-50 p-6">
            <p class="text-xs font-extrabold text-cyan-700 uppercase">Psikomotorik</p>
            <h3 class="font-extrabold text-slate-900 mt-2">Kemampuan Praktik</h3>
            <p class="text-3xl font-extrabold text-slate-900 mt-4">50%</p>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
        <h2 class="text-2xl font-extrabold text-slate-900">Rumus Nilai Akhir</h2>

        <div class="mt-5 rounded-3xl bg-slate-50 border border-slate-100 p-6 text-slate-700 leading-relaxed">
            <p>
                <span class="font-extrabold">Nilai Angka</span>
                = Kehadiran × 10% + Laporan × 10% + Responsi × 10% + Asistensi × 20% + Kemampuan Praktik × 50%.
            </p>

            <p class="mt-3 text-sm text-slate-500">
                Halaman ini masih berupa konfigurasi tetap karena tabel kategori penilaian belum tersedia di database.
            </p>
        </div>
    </section>

</div>
@endsection