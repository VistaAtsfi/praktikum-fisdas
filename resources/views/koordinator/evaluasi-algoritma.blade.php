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

    $chartMemoryData = $chartMemory ?? [
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

            <div class="flex flex-wrap gap-3">
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

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6 max-w-full">
        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 min-w-0">
            <h2 class="text-xl font-extrabold text-slate-900">
                Perbandingan Waktu Sorting
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Mean waktu eksekusi setelah outlier IQR dibuang.
                Merge Sort dibandingkan dengan baseline MySQL ORDER BY.
            </p>

            <div class="h-80 mt-6 max-w-full">
                <canvas id="sortingChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-blue-100 shadow-sm p-6 min-w-0">
            <h2 class="text-xl font-extrabold text-slate-900">
                Perbandingan Waktu Searching
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Sequential Search dibandingkan dengan baseline MySQL WHERE/LIKE.
            </p>

            <div class="h-80 mt-6 max-w-full">
                <canvas id="searchingChart"></canvas>
            </div>
        </div>

        <div class="xl:col-span-2 bg-white rounded-3xl border border-blue-100 shadow-sm p-6 min-w-0">
            <h2 class="text-xl font-extrabold text-slate-900">
                Perbandingan Penggunaan Memori
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Grafik memakai kenaikan memori selama proses pengujian berjalan, sehingga perbedaan metode
                lebih mudah dibandingkan.
            </p>

            <div class="h-96 mt-6 max-w-full">
                <canvas id="memoryChart"></canvas>
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
                                        <span class="block truncate" title="{{ $labelMetode($row['method']) }}">
                                            {{ $labelMetode($row['method']) }}
                                        </span>

                                        <span class="md:hidden block text-[10px] text-slate-400 font-normal mt-1 truncate">
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
                                <span class="block truncate" title="{{ $labelMetode($result->method) }}">
                                    {{ $labelMetode($result->method) }}
                                </span>

                                <span class="md:hidden block text-[10px] text-slate-400 font-normal mt-1 truncate">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const sortingChartData = @json($chartSortingData);
    const searchingChartData = @json($chartSearchingData);
    const memoryChartData = @json($chartMemoryData);

    function formatIndonesia(value, fraction = 6) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: fraction,
            maximumFractionDigits: fraction
        }).format(value ?? 0);
    }

    function makeLineChart(canvasId, chartData, labelY, satuan = 'ms') {
        const canvas = document.getElementById(canvasId);

        if (!canvas) {
            return;
        }

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets.map((dataset) => ({
                    label: dataset.label,
                    data: dataset.data,
                    borderWidth: 3,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }))
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
                            boxWidth: 14,
                            padding: 18,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y ?? 0;

                                return context.dataset.label + ': ' + formatIndonesia(value, 6) + ' ' + satuan;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Ukuran Dataset'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: labelY
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

    function makeBarChart(canvasId, chartData, labelY, satuan = 'KB') {
        const canvas = document.getElementById(canvasId);

        if (!canvas) {
            return;
        }

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets.map((dataset) => ({
                    label: dataset.label,
                    data: dataset.data,
                    borderWidth: 2
                }))
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
                            boxWidth: 14,
                            padding: 18,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y ?? 0;

                                return context.dataset.label + ': ' + formatIndonesia(value, 4) + ' ' + satuan;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Ukuran Dataset'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: labelY
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

    makeLineChart('sortingChart', sortingChartData, 'Waktu Eksekusi (ms)', 'ms');
    makeLineChart('searchingChart', searchingChartData, 'Waktu Eksekusi (ms)', 'ms');
    makeBarChart('memoryChart', memoryChartData, 'Memori (KB)', 'KB');
</script>
@endsection