<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Koordinator Praktikum
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Selamat datang, {{ auth()->user()->name }}</h3>

                <div class="space-y-2">
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Role:</strong> {{ auth()->user()->role }}</p>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Kelola Praktikum</h4>
                        <p class="text-sm text-gray-600">Fisika Dasar 1 dan Fisika Dasar 2.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Kelola Praktikan</h4>
                        <p class="text-sm text-gray-600">Lihat dan kelola data praktikan.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Rekap Nilai</h4>
                        <p class="text-sm text-gray-600">Lihat nilai harian dan nilai akhir.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Log Merge Sort</h4>
                        <p class="text-sm text-gray-600">Lihat hasil pengujian pengurutan.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Log Sequential Search</h4>
                        <p class="text-sm text-gray-600">Lihat hasil pengujian pencarian.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold">Data Presensi</h4>
                        <p class="text-sm text-gray-600">Lihat hasil scan QR praktikan.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>