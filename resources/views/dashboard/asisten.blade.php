<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Asisten
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Selamat datang, {{ auth()->user()->name }}</h3>

                <div class="space-y-2">
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Role:</strong> {{ auth()->user()->role }}</p>

                    @if ($asisten)
                        <p><strong>Praktikum:</strong> {{ $asisten->praktikum->nama ?? '-' }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($asisten->status) }}</p>
                    @else
                        <p class="text-red-600">Data asisten belum tersedia.</p>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Scan QR Presensi</h4>
                        <p class="text-sm text-gray-600">Scan QR praktikan untuk mencatat kehadiran.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Input Nilai</h4>
                        <p class="text-sm text-gray-600">Input nilai sesuai spreadsheet.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Data Praktikan</h4>
                        <p class="text-sm text-gray-600">Lihat daftar praktikan.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>