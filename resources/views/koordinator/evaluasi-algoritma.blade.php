@extends('layouts.koordinator', ['title' => 'Evaluasi Algoritma'])

@section('content')
@php
    $rawResults = $rawResults ?? collect();
    $results = $results ?? collect();

    $formatAngka = function ($value, int $precision = 6) {
        if ($value === null || $value === '') {
            $value = 0;
        }

        return number_format((float) $value, $precision, ',', '.');
    };

    $formatBilangan = function ($value) {
        if ($value === null || $value === '') {
            $value = 0;
        }

        return number_format((float) $value, 0, ',', '.');
    };

    $waktuEksekusi = function ($result) {
        return (float) (
            $result->sort_time
            ?? $result->search_time
            ?? $result->baseline_time
            ?? $result->execution_time_ms
            ?? 0
        );
    };

    $memoriEksekusi = function ($result) {
        return (float) (
            $result->memory_usage
            ?? $result->memory_usage_kb
            ?? 0
        );
    };

    $labelMetode = function ($method) {
        return match ($method) {
            'merge_sort_application' => 'Merge Sort',
            'sequential_search_application' => 'Sequential Search',
            'mysql_order_by' => 'MySQL ORDER BY',
            'mysql_where_like' => 'MySQL WHERE/LIKE',
            default => str_replace('_', ' ', $method),
        };
    };

    $dotMetode = function ($method) {
        return match ($method) {
            'merge_sort_application' => 'bg-blue-600',
            'sequential_search_application' => 'bg-emerald-500',
            'mysql_order_by' => 'bg-rose-500',
            'mysql_where_like' => 'bg-amber-500',
            default => 'bg-slate-400',
        };
    };

    $labelSkenario = function ($scenario) {
        return match ($scenario) {
            'random_ascending' => 'Random Asc',
            'random_descending' => 'Random Desc',
            'already_sorted' => 'Sudah Urut',
            'duplicate_value' => 'Duplikat',
            'keyword_found' => 'Keyword Ada',
            'keyword_not_found' => 'Tidak Ada',
            'keyword_empty' => 'Kosong',
            'duplicate_keyword' => 'Duplikat',
            default => str_replace('_', ' ', $scenario),
        };
    };

    $totalDatasetSintetis = $totalDatasetSintetis ?? collect($datasetRows ?? [])->max('available') ?? 0;
    $rawRunCount = $rawRunCount ?? $rawResults->count();
    $cleanRunCount = $cleanRunCount ?? $results->count();
    $outlierRunCount = $outlierRunCount ?? $rawResults->where('is_outlier', true)->count();
    $validCleanCount = $validCleanCount ?? $results->where('is_valid', true)->count();
    $invalidCleanCount = $invalidCleanCount ?? $results->where('is_valid', false)->count();
    $totalBatch = $totalBatch ?? $rawResults->pluck('batch_code')->unique()->count();

    $chartSortingData = $chartSorting ?? [
        'labels' => [],
        'datasets' => [],
    ];

    $chartSearchingData = $chartSearching ?? [
        'labels' => [],
        'datasets' => [],
    ];

    $chartMemorySortingData = $chartMemorySorting ?? [
        'labels' => [],
        'datasets' => [],
    ];

    $chartMemorySearchingData = $chartMemorySearching ?? [
        'labels' => [],
        'datasets' => [],
    ];

    $summaryByDataset = collect($summaryRows ?? [])
        ->groupBy('dataset_size')
        ->sortKeys();

    $latestResults = $latestResults ?? collect();
@endphp

<div class="space-y-8 w-full max-w-full overflow-x-hidden">
    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 max-w-full">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900">
                    Evaluasi Algoritma
                </h1>

                <p class="text-slate-500 mt-2 max-w-3xl">
                    Halaman ini menampilkan hasil pengujian data sintetis untuk Merge Sort,
                    Sequential Search, dan baseline MySQL menggunakan ORDER BY serta WHERE/LIKE.
                </p>

                <p class="text-slate-500 mt-2 max-w-3xl">
                    Data warm-up tidak dihitung. Outlier ditandai menggunakan metode IQR,
                    lalu grafik dan statistik memakai data bersih setelah outlier dibuang.
                </p>

                @if ($latestBatch)
                    <p class="text-sm text-slate-400 mt-2">
                        Batch terakhir:
                        <span class="font-bold text-slate-700">
                            {{ $latestBatch }}
                        </span>
                    </p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button"
                        id="btnJalankanPengujian"
                        onclick="bukaModalKonfirmasi()"
                        class="px-5 py-3 rounded-2xl bg-emerald-600 text-white font-extrabold hover:bg-emerald-700 transition flex items-center gap-2 cursor-pointer shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Jalankan Pengujian</span>
                </button>

                <a href="{{ route('koordinator.evaluasi-algoritma.export') }}"
                   class="px-5 py-3 rounded-2xl bg-blue-700 text-white font-extrabold hover:bg-blue-800 transition">
                    Export ZIP CSV
                </a>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <section class="bg-red-50 border border-red-100 rounded-3xl p-6 max-w-full">
            <h2 class="font-extrabold text-red-700 mb-3">
                Terjadi kesalahan:
            </h2>

            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 max-w-full">
        <h2 class="text-2xl font-extrabold text-slate-900">
            Dataset Sintetis
        </h2>

        <p class="text-slate-500 mt-2">
            Data ini berasal dari user sintetis dengan email
            <span class="font-bold text-slate-700">benchmark.test</span>,
            bukan data praktikan asli.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mt-6">
            @foreach ($datasetRows as $dataset)
                <div class="rounded-3xl border border-blue-100 bg-blue-50 p-5">
                    <p class="text-sm font-extrabold text-blue-700 uppercase">
                        Dataset {{ $dataset['size'] }}
                    </p>

                    <h3 class="text-3xl font-black text-slate-900 mt-2">
                        {{ $formatBilangan($dataset['available']) }}
                    </h3>

                    <p class="text-slate-500 text-sm mt-2">
                        Data tersedia untuk pengujian.
                    </p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5 max-w-full">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm font-extrabold text-blue-700 uppercase">
                Total Dataset Sintetis
            </p>

            <h2 class="text-4xl font-black text-slate-900 mt-3">
                {{ $formatBilangan($totalDatasetSintetis) }}
            </h2>

            <p class="text-slate-500 mt-2">
                Jumlah user sintetis benchmark.
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm font-extrabold text-blue-700 uppercase">
                Run Mentah
            </p>

            <h2 class="text-4xl font-black text-slate-900 mt-3">
                {{ $formatBilangan($rawRunCount) }}
            </h2>

            <p class="text-slate-500 mt-2">
                Run utama sebelum IQR.
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm font-extrabold text-blue-700 uppercase">
                Run Bersih IQR
            </p>

            <h2 class="text-4xl font-black text-slate-900 mt-3">
                {{ $formatBilangan($cleanRunCount) }}
            </h2>

            <p class="text-slate-500 mt-2">
                Data yang dipakai di grafik.
            </p>
        </div>

        <div class="bg-yellow-50 rounded-3xl border border-yellow-100 shadow-sm p-6">
            <p class="text-sm font-extrabold text-yellow-700 uppercase">
                Dibuang IQR
            </p>

            <h2 class="text-4xl font-black text-slate-900 mt-3">
                {{ $formatBilangan($outlierRunCount) }}
            </h2>

            <p class="text-slate-500 mt-2">
                Outlier tidak dihitung.
            </p>
        </div>

        <div class="bg-green-50 rounded-3xl border border-green-100 shadow-sm p-6">
            <p class="text-sm font-extrabold text-green-700 uppercase">
                Valid
            </p>

            <h2 class="text-4xl font-black text-slate-900 mt-3">
                {{ $formatBilangan($validCleanCount) }}
            </h2>

            <p class="text-slate-500 mt-2">
                Hasil sesuai skenario.
            </p>
        </div>
    </section>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 max-w-full">
        <div class="bg-red-50 rounded-3xl border border-red-100 shadow-sm p-6">
            <p class="text-sm font-extrabold text-red-700 uppercase">
                Tidak Valid
            </p>

            <h2 class="text-4xl font-black text-slate-900 mt-3">
                {{ $formatBilangan($invalidCleanCount) }}
            </h2>

            <p class="text-slate-500 mt-2">
                Data bersih yang tidak sesuai skenario.
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm font-extrabold text-blue-700 uppercase">
                Total Batch
            </p>

            <h2 class="text-4xl font-black text-slate-900 mt-3">
                {{ $formatBilangan($totalBatch) }}
            </h2>

            <p class="text-slate-500 mt-2">
                Kelompok pengujian.
            </p>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6">
            <p class="text-sm font-extrabold text-blue-700 uppercase">
                Status Dataset
            </p>

            <h2 class="text-4xl font-black text-slate-900 mt-3">
                {{ $totalDatasetSintetis > 0 ? 'Ada' : 'Kosong' }}
            </h2>

            <p class="text-slate-500 mt-2">
                Jalankan command benchmark jika masih kosong.
            </p>
        </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6 max-w-full">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 min-w-0">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                <h2 class="text-xl font-extrabold text-slate-900">
                    Perbandingan Waktu Sorting
                </h2>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                    Waktu (ms)
                </span>
            </div>

            <p class="text-sm text-slate-500">
                Mean waktu eksekusi setelah outlier IQR dibuang.
                Merge Sort dibandingkan dengan baseline MySQL ORDER BY.
            </p>

            <div class="h-80 mt-6 max-w-full">
                <canvas id="sortingChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 min-w-0">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                <h2 class="text-xl font-extrabold text-slate-900">
                    Perbandingan Waktu Searching
                </h2>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-100">
                    Waktu (ms)
                </span>
            </div>

            <p class="text-sm text-slate-500">
                Sequential Search dibandingkan dengan baseline MySQL WHERE/LIKE.
            </p>

            <div class="h-80 mt-6 max-w-full">
                <canvas id="searchingChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 min-w-0">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                <h2 class="text-xl font-extrabold text-slate-900">
                    Penggunaan Memori Sorting
                </h2>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                    Memori (KB)
                </span>
            </div>

            <p class="text-sm text-slate-500">
                Mean penggunaan memori Merge Sort dibandingkan dengan baseline MySQL ORDER BY.
            </p>

            <div class="h-80 mt-6 max-w-full">
                <canvas id="memorySortingChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 min-w-0">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
                <h2 class="text-xl font-extrabold text-slate-900">
                    Penggunaan Memori Searching
                </h2>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-100">
                    Memori (KB)
                </span>
            </div>

            <p class="text-sm text-slate-500">
                Mean penggunaan memori Sequential Search dibandingkan dengan baseline MySQL WHERE/LIKE.
            </p>

            <div class="h-80 mt-6 max-w-full">
                <canvas id="memorySearchingChart"></canvas>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden max-w-full">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Statistik Ringkasan
            </h2>

            <p class="text-slate-500 mt-2">
                Tabel dipisahkan berdasarkan ukuran dataset agar hasil 100, 500, 1000, dan 5000 data
                lebih mudah dibandingkan.
            </p>
        </div>

        <div class="p-4 sm:p-6 space-y-8 max-w-full overflow-x-hidden">
            @forelse ($summaryByDataset as $datasetSize => $rows)
                <div class="rounded-3xl border border-blue-100 overflow-hidden max-w-full">
                    <div class="px-4 py-4 bg-blue-50 border-b border-blue-100">
                        <h3 class="text-xl font-black text-slate-900">
                            Dataset {{ $formatBilangan($datasetSize) }} Data
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            Run bersih adalah jumlah run setelah outlier IQR dibuang.
                        </p>
                    </div>

                    <table class="w-full table-fixed text-[10px] sm:text-xs">
                        <thead class="bg-slate-100 text-slate-700">
                            <tr>
                                <th class="px-2 sm:px-3 py-3 text-left w-[12%]">
                                    Tipe
                                </th>
                                <th class="px-2 sm:px-3 py-3 text-left w-[17%]">
                                    Metode
                                </th>
                                <th class="px-2 sm:px-3 py-3 text-left w-[14%] hidden md:table-cell">
                                    Skenario
                                </th>
                                <th class="px-2 sm:px-3 py-3 text-right w-[10%]">
                                    Run
                                </th>
                                <th class="px-2 sm:px-3 py-3 text-right w-[9%]">
                                    IQR
                                </th>
                                <th class="px-2 sm:px-3 py-3 text-right w-[12%]">
                                    Mean
                                </th>
                                <th class="px-2 sm:px-3 py-3 text-right w-[12%] hidden lg:table-cell">
                                    Median
                                </th>
                                <th class="px-2 sm:px-3 py-3 text-right w-[12%] hidden lg:table-cell">
                                    Std Dev
                                </th>
                                <th class="px-2 sm:px-3 py-3 text-right w-[12%]">
                                    Memori
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-blue-50">
                            @foreach ($rows as $row)
                                <tr class="hover:bg-blue-50/40">
                                    <td class="px-2 sm:px-3 py-3 font-bold text-slate-700">
                                        {{ ucfirst($row['test_type']) }}
                                    </td>

                                    <td class="px-2 sm:px-3 py-3 font-bold text-slate-800">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $dotMetode($row['method']) }}"></span>
                                            <span class="block truncate" title="{{ $labelMetode($row['method']) }}">
                                                {{ $labelMetode($row['method']) }}
                                            </span>
                                        </div>

                                        <span class="md:hidden block text-[10px] text-slate-400 font-normal mt-1 truncate pl-4">
                                            {{ $labelSkenario($row['scenario']) }}
                                        </span>
                                    </td>

                                    <td class="px-2 sm:px-3 py-3 text-slate-600 hidden md:table-cell">
                                        <span class="block truncate" title="{{ $labelSkenario($row['scenario']) }}">
                                            {{ $labelSkenario($row['scenario']) }}
                                        </span>
                                    </td>

                                    <td class="px-2 sm:px-3 py-3 text-right text-slate-700 whitespace-nowrap">
                                        {{ $formatBilangan($row['clean_runs']) }}/{{ $formatBilangan($row['total_runs']) }}
                                    </td>

                                    <td class="px-2 sm:px-3 py-3 text-right">
                                        @if (($row['outlier_count'] ?? 0) > 0)
                                            <span class="inline-flex items-center justify-end px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-extrabold">
                                                {{ $formatBilangan($row['outlier_count']) }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-bold">
                                                0
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-2 sm:px-3 py-3 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                        {{ $formatAngka($row['mean_time_ms'], 6) }}
                                    </td>

                                    <td class="px-2 sm:px-3 py-3 text-right text-slate-700 whitespace-nowrap hidden lg:table-cell">
                                        {{ $formatAngka($row['median_time_ms'], 6) }}
                                    </td>

                                    <td class="px-2 sm:px-3 py-3 text-right text-slate-700 whitespace-nowrap hidden lg:table-cell">
                                        {{ $formatAngka($row['std_dev_time_ms'], 6) }}
                                    </td>

                                    <td class="px-2 sm:px-3 py-3 text-right text-slate-700 whitespace-nowrap">
                                        {{ $formatAngka($row['mean_memory_kb'], 4) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @empty
                <div class="rounded-3xl border border-blue-100 p-8 text-center text-slate-500">
                    Belum ada hasil pengujian. Jalankan command benchmark terlebih dahulu.
                </div>
            @endforelse
        </div>
    </section>

    <section class="bg-white rounded-3xl border border-blue-100 shadow-sm overflow-hidden max-w-full">
        <div class="p-6 border-b border-blue-100">
            <h2 class="text-2xl font-extrabold text-slate-900">
                Hasil Pengujian Terbaru
            </h2>

            <p class="text-slate-500 mt-2">
                Data terbaru yang ditampilkan adalah data bersih setelah warm-up dan outlier tidak dihitung.
            </p>
        </div>

        <div class="p-4 sm:p-6 max-w-full overflow-x-hidden">
            <table class="w-full table-fixed text-[10px] sm:text-xs">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="px-2 sm:px-3 py-3 text-left w-[12%]">
                            Data
                        </th>
                        <th class="px-2 sm:px-3 py-3 text-left w-[12%]">
                            Tipe
                        </th>
                        <th class="px-2 sm:px-3 py-3 text-left w-[22%]">
                            Metode
                        </th>
                        <th class="px-2 sm:px-3 py-3 text-left w-[18%] hidden md:table-cell">
                            Skenario
                        </th>
                        <th class="px-2 sm:px-3 py-3 text-right w-[14%]">
                            Waktu
                        </th>
                        <th class="px-2 sm:px-3 py-3 text-right w-[14%]">
                            Memori
                        </th>
                        <th class="px-2 sm:px-3 py-3 text-center w-[8%]">
                            Valid
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-blue-50">
                    @forelse ($latestResults as $result)
                        <tr class="hover:bg-blue-50/40">
                            <td class="px-2 sm:px-3 py-3 font-bold text-slate-800">
                                {{ $formatBilangan($result->dataset_size) }}
                            </td>

                            <td class="px-2 sm:px-3 py-3 text-slate-600">
                                {{ ucfirst($result->test_type) }}
                            </td>

                            <td class="px-2 sm:px-3 py-3 text-slate-700 font-bold">
                                <div class="flex items-center gap-1.5 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $dotMetode($result->method) }}"></span>
                                    <span class="block truncate" title="{{ $labelMetode($result->method) }}">
                                        {{ $labelMetode($result->method) }}
                                    </span>
                                </div>

                                <span class="md:hidden block text-[10px] text-slate-400 font-normal mt-1 truncate pl-4">
                                    {{ $labelSkenario($result->scenario) }}
                                </span>
                            </td>

                            <td class="px-2 sm:px-3 py-3 text-slate-600 hidden md:table-cell">
                                <span class="block truncate" title="{{ $labelSkenario($result->scenario) }}">
                                    {{ $labelSkenario($result->scenario) }}
                                </span>
                            </td>

                            <td class="px-2 sm:px-3 py-3 text-right font-bold text-slate-900 whitespace-nowrap">
                                {{ $formatAngka($waktuEksekusi($result), 6) }}
                            </td>

                            <td class="px-2 sm:px-3 py-3 text-right text-slate-700 whitespace-nowrap">
                                {{ $formatAngka($memoriEksekusi($result), 4) }}
                            </td>

                            <td class="px-2 sm:px-3 py-3 text-center">
                                @if ($result->is_valid)
                                    <span class="inline-flex px-2 py-1 rounded-full bg-green-100 text-green-700 font-bold">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded-full bg-red-100 text-red-700 font-bold">
                                        Tidak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                Belum ada data terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Modal Konfirmasi Jalankan Pengujian --}}
    <div id="modalKonfirmasiPengujian"
         onclick="if(event.target === this) tutupModalKonfirmasi()"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4">
        <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl border border-blue-100 p-6 sm:p-8">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900">
                        Jalankan Pengujian Algoritma?
                    </h3>
                    <p class="text-slate-500 mt-2 text-sm leading-relaxed">
                        Sistem akan menjalankan pengujian komparasi <strong>Merge Sort</strong> vs <strong>MySQL ORDER BY</strong> dan <strong>Sequential Search</strong> vs <strong>MySQL WHERE/LIKE</strong>.
                    </p>
                </div>
            </div>

            <div class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs text-slate-600 space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span><strong>Ukuran Dataset:</strong> 100, 500, 1.000, dan 5.000 data</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span><strong>Skenario:</strong> Seluruh skenario pengurutan & pencarian dijalankan otomatis</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span><strong>Pengulangan:</strong> 50 run utama + 10 warm-up (eliminasi outlier IQR otomatis)</span>
                </div>
            </div>

            <p class="text-xs text-amber-600 mt-3 flex items-center gap-1.5 font-medium">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Proses ini membutuhkan waktu beberapa menit. Harap jangan menutup halaman saat berjalan.</span>
            </p>

            <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-3">
                <button type="button"
                        onclick="tutupModalKonfirmasi()"
                        class="px-5 py-3 rounded-2xl bg-slate-100 text-slate-700 font-extrabold hover:bg-slate-200 transition cursor-pointer">
                    Batal
                </button>

                <button type="button"
                        id="btnEksekusiPengujian"
                        onclick="eksekusiPengujian()"
                        class="px-5 py-3 rounded-2xl bg-emerald-600 text-white font-extrabold hover:bg-emerald-700 transition cursor-pointer shadow-sm">
                    Ya, Jalankan Sekarang
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Loading / Sedang Memproses --}}
    <div id="modalLoadingPengujian"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-xs px-4">
        <div class="w-full max-w-md rounded-3xl bg-white shadow-2xl border border-blue-100 p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 mb-5">
                <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <h3 class="text-2xl font-extrabold text-slate-900">
                Pengujian Sedang Berjalan...
            </h3>

            <p class="text-slate-500 mt-2 text-sm leading-relaxed">
                Sedang memproses seluruh skenario pada dataset 100, 500, 1.000, dan 5.000 data serta menghitung outlier IQR.
            </p>

            <div class="mt-5 py-3 px-4 rounded-2xl bg-slate-50 border border-slate-100 inline-flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500">Waktu Berjalan:</span>
                <span class="font-mono font-bold text-slate-800 text-sm" id="benchmarkTimer">00:00</span>
            </div>

            <p class="text-xs text-slate-400 mt-4">
                Mohon tunggu dan jangan menutup atau merefresh tab peramban ini.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const sortingChartData = @json($chartSortingData);
    const searchingChartData = @json($chartSearchingData);
    const memorySortingChartData = @json($chartMemorySortingData);
    const memorySearchingChartData = @json($chartMemorySearchingData);

    const METHOD_CONFIG = {
        'Sequential Search': {
            borderColor: '#10b981',       // Emerald Green 500
            backgroundColor: 'rgba(16, 185, 129, 0.12)',
            pointBorderColor: '#059669',   // Emerald Green 600
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#10b981',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'circle',
            category: 'searching'
        },
        'sequential_search_application': {
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.12)',
            pointBorderColor: '#059669',
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#10b981',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'circle',
            category: 'searching'
        },
        'Merge Sort': {
            borderColor: '#2563eb',       // Royal Blue 600
            backgroundColor: 'rgba(37, 99, 235, 0.12)',
            pointBorderColor: '#1d4ed8',   // Royal Blue 700
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#2563eb',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'circle',
            category: 'sorting'
        },
        'merge_sort_application': {
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.12)',
            pointBorderColor: '#1d4ed8',
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#2563eb',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'circle',
            category: 'sorting'
        },
        'MySQL ORDER BY': {
            borderColor: '#f43f5e',       // Rose 500
            backgroundColor: 'rgba(244, 63, 94, 0.12)',
            pointBorderColor: '#e11d48',   // Rose 600
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#f43f5e',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'rectRot',
            category: 'sorting'
        },
        'mysql_order_by': {
            borderColor: '#f43f5e',
            backgroundColor: 'rgba(244, 63, 94, 0.12)',
            pointBorderColor: '#e11d48',
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#f43f5e',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'rectRot',
            category: 'sorting'
        },
        'MySQL WHERE/LIKE': {
            borderColor: '#f59e0b',       // Amber 500
            backgroundColor: 'rgba(245, 158, 11, 0.12)',
            pointBorderColor: '#d97706',   // Amber 600
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#f59e0b',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'rect',
            category: 'searching'
        },
        'mysql_where_like': {
            borderColor: '#f59e0b',
            backgroundColor: 'rgba(245, 158, 11, 0.12)',
            pointBorderColor: '#d97706',
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#f59e0b',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'rect',
            category: 'searching'
        }
    };

    function getConfigForDataset(dataset) {
        const key = dataset.method || dataset.label;
        if (METHOD_CONFIG[key]) {
            return METHOD_CONFIG[key];
        }
        if (METHOD_CONFIG[dataset.label]) {
            return METHOD_CONFIG[dataset.label];
        }
        return {
            borderColor: '#64748b',
            backgroundColor: 'rgba(100, 116, 139, 0.12)',
            pointBorderColor: '#475569',
            pointBackgroundColor: '#ffffff',
            pointHoverBackgroundColor: '#64748b',
            pointHoverBorderColor: '#ffffff',
            pointStyle: 'circle',
            category: 'other'
        };
    }

    function formatIndonesia(value, fraction = 6) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: fraction,
            maximumFractionDigits: fraction
        }).format(value ?? 0);
    }

    function createLineDataset(dataset) {
        const cfg = getConfigForDataset(dataset);
        return {
            label: dataset.label,
            method: dataset.method,
            category: cfg.category,
            data: dataset.data,
            borderColor: cfg.borderColor,
            backgroundColor: cfg.backgroundColor,
            pointBorderColor: cfg.pointBorderColor,
            pointBackgroundColor: cfg.pointBackgroundColor,
            pointHoverBackgroundColor: cfg.pointHoverBackgroundColor,
            pointHoverBorderColor: cfg.pointHoverBorderColor,
            pointRadius: 6,
            pointHoverRadius: 9,
            pointBorderWidth: 2.5,
            pointStyle: cfg.pointStyle,
            borderWidth: 3,
            tension: 0.35,
            fill: false
        };
    }

    function makeLineChart(canvasId, chartData, labelY, satuan = 'ms', isLog = false) {
        const canvas = document.getElementById(canvasId);

        if (!canvas) {
            return null;
        }

        return new Chart(canvas, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets.map(createLineDataset)
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 16,
                            boxHeight: 16,
                            usePointStyle: true,
                            pointStyleWidth: 14,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 12,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y ?? 0;
                                const precision = satuan === 'KB' ? 4 : 6;
                                return ' ' + context.dataset.label + ': ' + formatIndonesia(value, precision) + ' ' + satuan;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(226, 232, 240, 0.6)',
                            drawBorder: false
                        },
                        title: {
                            display: true,
                            text: 'Ukuran Dataset (Jumlah Data)',
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            padding: { top: 10 }
                        }
                    },
                    y: {
                        /*
                        |--------------------------------------------------------------------------
                        | Penerapan Skala Logaritmik (Sumbu Y)
                        |--------------------------------------------------------------------------
                        | - Jika isLog = true : Menggunakan type 'logarithmic' (kelipatan perkalian 10:
                        |   0.1 -> 1 -> 10 -> 100 -> 1.000 -> 10.000 KB). Sangat berguna agar data
                        |   memori bernilai kecil (Sequential Search ~0.1 KB) tidak tenggelam/menempel
                        |   di garis dasar oleh data yang sangat besar (MySQL ORDER BY ~3.800 KB).
                        | - Jika isLog = false: Menggunakan type 'linear' (skala normal penjumlahan tetap).
                        | - beginAtZero: Harus false pada skala logaritmik karena log(0) tidak terdefinisi secara matematika.
                        */
                        type: isLog ? 'logarithmic' : 'linear',
                        beginAtZero: !isLog,
                        grid: {
                            color: 'rgba(226, 232, 240, 0.6)',
                            drawBorder: false
                        },
                        title: {
                            display: true,
                            text: labelY + (isLog ? ' (Skala Log)' : ''),
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            padding: { bottom: 10 }
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    const sortingChart = makeLineChart('sortingChart', sortingChartData, 'Waktu Eksekusi (ms)', 'ms');
    const searchingChart = makeLineChart('searchingChart', searchingChartData, 'Waktu Eksekusi (ms)', 'ms');
    const memorySortingChart = makeLineChart('memorySortingChart', memorySortingChartData, 'Penggunaan Memori (KB)', 'KB');
    const memorySearchingChart = makeLineChart('memorySearchingChart', memorySearchingChartData, 'Penggunaan Memori (KB)', 'KB');

    let benchmarkTimerInterval = null;

    function bukaModalKonfirmasi() {
        const modal = document.getElementById('modalKonfirmasiPengujian');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function tutupModalKonfirmasi() {
        const modal = document.getElementById('modalKonfirmasiPengujian');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function eksekusiPengujian() {
        tutupModalKonfirmasi();

        const loadingModal = document.getElementById('modalLoadingPengujian');
        if (loadingModal) {
            loadingModal.classList.remove('hidden');
            loadingModal.classList.add('flex');
        }

        const timerEl = document.getElementById('benchmarkTimer');
        let elapsed = 0;
        if (timerEl) {
            timerEl.textContent = '00:00';
            benchmarkTimerInterval = setInterval(() => {
                elapsed++;
                const mins = String(Math.floor(elapsed / 60)).padStart(2, '0');
                const secs = String(elapsed % 60).padStart(2, '0');
                timerEl.textContent = `${mins}:${secs}`;
            }, 1000);
        }

        const btnTrigger = document.getElementById('btnJalankanPengujian');
        if (btnTrigger) {
            btnTrigger.disabled = true;
            btnTrigger.classList.add('opacity-60', 'cursor-not-allowed');
        }

        fetch("{{ route('koordinator.evaluasi-algoritma.run') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({}),
        })
        .then(async (response) => {
            const data = await response.json().catch(() => null);
            if (!response.ok) {
                throw new Error((data && data.message) ? data.message : ('Terjadi kesalahan pada server (Status ' + response.status + ')'));
            }
            return data;
        })
        .then((data) => {
            if (benchmarkTimerInterval) clearInterval(benchmarkTimerInterval);
            window.location.reload();
        })
        .catch((error) => {
            if (benchmarkTimerInterval) clearInterval(benchmarkTimerInterval);
            if (loadingModal) {
                loadingModal.classList.add('hidden');
                loadingModal.classList.remove('flex');
            }
            if (btnTrigger) {
                btnTrigger.disabled = false;
                btnTrigger.classList.remove('opacity-60', 'cursor-not-allowed');
            }
            alert('Pengujian gagal: ' + error.message);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const loadingModal = document.getElementById('modalLoadingPengujian');
            if (loadingModal && loadingModal.classList.contains('hidden')) {
                tutupModalKonfirmasi();
            }
        }
    });
</script>
@endsection