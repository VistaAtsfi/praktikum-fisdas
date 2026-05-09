<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Praktikan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Selamat datang, {{ auth()->user()->name }}</h3>

                <div class="space-y-2">
                    <p><strong>NIM:</strong> {{ auth()->user()->nim }}</p>
                    <p><strong>Program Studi:</strong> {{ auth()->user()->prodi }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>

                    @if ($praktikan)
                        <p><strong>Praktikum:</strong> {{ $praktikan->praktikum->nama ?? '-' }}</p>
                        <p><strong>Kelompok:</strong> {{ $praktikan->kelompok->nama_kelompok ?? 'Belum ditentukan' }}</p>
                        <p><strong>QR Code:</strong> {{ $praktikan->qr_code }}</p>
                    @else
                        <p class="text-red-600">Data praktikan belum tersedia.</p>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Materi</h4>
                        <p class="text-sm text-gray-600">Lihat materi praktikum.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Presensi</h4>
                        <p class="text-sm text-gray-600">Lihat riwayat kehadiran.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Nilai</h4>
                        <p class="text-sm text-gray-600">Lihat nilai praktikum.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>