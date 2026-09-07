<?php

namespace App\Http\Controllers;

use App\Models\PerformanceLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use ZipArchive;

class AlgorithmEvaluationController extends Controller
{
    private function pastikanKoordinator(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'koordinator', 403);
    }

    public function index(): \Illuminate\View\View
    {
        $this->pastikanKoordinator();

        $rawResults = PerformanceLog::query()
            ->where('is_warmup', false)
            ->latest('timestamp')
            ->get();

        $results = PerformanceLog::query()
            ->where('is_warmup', false)
            ->where('is_outlier', false)
            ->latest('timestamp')
            ->get();

        $latestBatch = PerformanceLog::query()
            ->latest('timestamp')
            ->value('batch_code');

        $summaryRows = $this->buatSummaryRows($rawResults);

        $totalDatasetSintetis = User::where('email', 'like', 'synthetic.praktikan.%@benchmark.test')->count();

        $datasetRows = collect([100, 500, 1000, 5000])->map(function ($size) {
            return [
                'size' => $size,
                'available' => User::where('email', 'like', 'synthetic.praktikan.%@benchmark.test')
                    ->orderBy('nim')
                    ->limit($size)
                    ->count(),
            ];
        })->values();

        $chartSorting = $this->buatChartRows($summaryRows, 'sorting');
        $chartSearching = $this->buatChartRows($summaryRows, 'searching');
        $chartMemory = $this->buatChartMemoryRows($summaryRows);

        $latestResults = PerformanceLog::query()
            ->where('is_warmup', false)
            ->where('is_outlier', false)
            ->latest('timestamp')
            ->limit(25)
            ->get();

        $rawRunCount = $rawResults->count();
        $cleanRunCount = $results->count();
        $outlierRunCount = $rawResults->where('is_outlier', true)->count();
        $validCleanCount = $results->where('is_valid', true)->count();
        $invalidCleanCount = $results->where('is_valid', false)->count();
        $totalBatch = $rawResults->pluck('batch_code')->unique()->count();

        return view('koordinator.evaluasi-algoritma', compact(
            'rawResults',
            'results',
            'latestBatch',
            'summaryRows',
            'datasetRows',
            'chartSorting',
            'chartSearching',
            'chartMemory',
            'latestResults',
            'totalDatasetSintetis',
            'rawRunCount',
            'cleanRunCount',
            'outlierRunCount',
            'validCleanCount',
            'invalidCleanCount',
            'totalBatch'
        ));
    }

    public function exportZip(Request $request)
    {
        $this->pastikanKoordinator();

        $results = PerformanceLog::query()
            ->where('is_warmup', false)
            ->orderBy('dataset_size')
            ->orderBy('test_type')
            ->orderBy('method')
            ->orderBy('scenario')
            ->orderBy('run_order')
            ->get();

        if ($results->isEmpty()) {
            return back()->withErrors([
                'export' => 'Belum ada hasil pengujian yang bisa diekspor. Jalankan command benchmark terlebih dahulu.',
            ]);
        }

        if (!class_exists(ZipArchive::class)) {
            return back()->withErrors([
                'export' => 'Ekstensi ZIP belum aktif di PHP. Aktifkan extension=zip di php.ini.',
            ]);
        }

        $timestamp = now()->format('Ymd_His');

        $folder = storage_path('app/benchmark_export_' . $timestamp);
        $zipPath = storage_path('app/evaluasi_algoritma_' . $timestamp . '.zip');

        File::ensureDirectoryExists($folder);

        $this->tulisCsv(
            $folder . '/performance_logs_raw.csv',
            [
                'timestamp',
                'batch_code',
                'dataset_size',
                'test_type',
                'method',
                'scenario',
                'field_name',
                'direction',
                'keyword',
                'run_order',
                'sort_time',
                'search_time',
                'baseline_time',
                'execution_time_ms',
                'memory_usage',
                'iteration_count',
                'comparison_count',
                'result_count',
                'is_valid',
                'is_outlier',
            ],
            $results->map(function ($row) {
                return [
                    optional($row->timestamp)->format('Y-m-d H:i:s'),
                    $row->batch_code,
                    $row->dataset_size,
                    $row->test_type,
                    $row->method,
                    $row->scenario,
                    $row->field_name,
                    $row->direction,
                    $row->keyword,
                    $row->run_order,
                    $row->sort_time,
                    $row->search_time,
                    $row->baseline_time,
                    $this->waktuEksekusi($row),
                    $row->memory_usage,
                    $row->iteration_count,
                    $row->comparison_count,
                    $row->result_count,
                    $row->is_valid ? 'valid' : 'tidak_valid',
                    $row->is_outlier ? 'outlier' : 'bersih',
                ];
            })->toArray()
        );

        $cleanResults = $results->where('is_outlier', false)->values();

        $this->tulisCsv(
            $folder . '/performance_logs_clean_iqr.csv',
            [
                'timestamp',
                'batch_code',
                'dataset_size',
                'test_type',
                'method',
                'scenario',
                'field_name',
                'direction',
                'keyword',
                'run_order',
                'sort_time',
                'search_time',
                'baseline_time',
                'execution_time_ms',
                'memory_usage',
                'iteration_count',
                'comparison_count',
                'result_count',
                'is_valid',
            ],
            $cleanResults->map(function ($row) {
                return [
                    optional($row->timestamp)->format('Y-m-d H:i:s'),
                    $row->batch_code,
                    $row->dataset_size,
                    $row->test_type,
                    $row->method,
                    $row->scenario,
                    $row->field_name,
                    $row->direction,
                    $row->keyword,
                    $row->run_order,
                    $row->sort_time,
                    $row->search_time,
                    $row->baseline_time,
                    $this->waktuEksekusi($row),
                    $row->memory_usage,
                    $row->iteration_count,
                    $row->comparison_count,
                    $row->result_count,
                    $row->is_valid ? 'valid' : 'tidak_valid',
                ];
            })->toArray()
        );

        $summaryRows = $this->buatSummaryRows($results);

        $this->tulisCsv(
            $folder . '/statistik_ringkasan.csv',
            [
                'dataset_size',
                'test_type',
                'method',
                'scenario',
                'total_runs',
                'clean_runs',
                'outlier_count',
                'mean_time_ms',
                'median_time_ms',
                'std_dev_time_ms',
                'q1_time_ms',
                'q3_time_ms',
                'iqr_time_ms',
                'mean_memory_kb',
                'median_memory_kb',
                'mean_iteration',
                'mean_comparison',
            ],
            collect($summaryRows)->map(function ($row) {
                return [
                    $row['dataset_size'],
                    $row['test_type'],
                    $row['method'],
                    $row['scenario'],
                    $row['total_runs'],
                    $row['clean_runs'],
                    $row['outlier_count'],
                    $row['mean_time_ms'],
                    $row['median_time_ms'],
                    $row['std_dev_time_ms'],
                    $row['q1_time_ms'],
                    $row['q3_time_ms'],
                    $row['iqr_time_ms'],
                    $row['mean_memory_kb'],
                    $row['median_memory_kb'],
                    $row['mean_iteration'],
                    $row['mean_comparison'],
                ];
            })->toArray()
        );

        foreach ([100, 500, 1000, 5000] as $size) {
            $dataset = User::query()
                ->select('id', 'nim', 'name', 'email', 'prodi', 'jenis_kelamin')
                ->where('email', 'like', 'synthetic.praktikan.%@benchmark.test')
                ->orderBy('nim')
                ->limit($size)
                ->get();

            $this->tulisCsv(
                $folder . '/dataset_sintetis_' . $size . '.csv',
                ['no', 'id', 'nim', 'nama', 'email', 'prodi', 'jenis_kelamin'],
                $dataset->values()->map(function ($user, $index) {
                    return [
                        $index + 1,
                        $user->id,
                        $user->nim,
                        $user->name,
                        $user->email,
                        $user->prodi,
                        $user->jenis_kelamin,
                    ];
                })->toArray()
            );
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->withErrors([
                'export' => 'ZIP gagal dibuat.',
            ]);
        }

        foreach (File::files($folder) as $file) {
            $zip->addFile($file->getPathname(), $file->getFilename());
        }

        $zip->close();

        File::deleteDirectory($folder);

        return response()
            ->download($zipPath, 'evaluasi_algoritma_' . $timestamp . '.zip')
            ->deleteFileAfterSend(true);
    }

    private function buatSummaryRows(Collection $results): array
    {
        return $results
            ->groupBy(function ($row) {
                return implode('|', [
                    $row->dataset_size,
                    $row->test_type,
                    $row->method,
                    $row->scenario,
                ]);
            })
            ->map(function ($items) {
                $cleanItems = $items->where('is_outlier', false)->values();

                $times = $cleanItems
                    ->map(fn ($item) => $this->waktuEksekusi($item))
                    ->values();

                $timeStats = $this->statistikIqr($times);

                $memoryStats = $this->statistikDasar(
                    $cleanItems->pluck('memory_usage')->map(fn ($value) => (float) $value)->values()
                );

                $iterationStats = $this->statistikDasar(
                    $cleanItems->pluck('iteration_count')->map(fn ($value) => (float) $value)->values()
                );

                $comparisonStats = $this->statistikDasar(
                    $cleanItems->pluck('comparison_count')->map(fn ($value) => (float) $value)->values()
                );

                $first = $items->first();

                return [
                    'dataset_size' => $first->dataset_size,
                    'test_type' => $first->test_type,
                    'method' => $first->method,
                    'scenario' => $first->scenario,
                    'total_runs' => $items->count(),
                    'clean_runs' => $cleanItems->count(),
                    'outlier_count' => $items->where('is_outlier', true)->count(),
                    'mean_time_ms' => $timeStats['mean'],
                    'median_time_ms' => $timeStats['median'],
                    'std_dev_time_ms' => $timeStats['std_dev'],
                    'q1_time_ms' => $timeStats['q1'],
                    'q3_time_ms' => $timeStats['q3'],
                    'iqr_time_ms' => $timeStats['iqr'],
                    'mean_memory_kb' => $memoryStats['mean'],
                    'median_memory_kb' => $memoryStats['median'],
                    'mean_iteration' => $iterationStats['mean'],
                    'mean_comparison' => $comparisonStats['mean'],
                ];
            })
            ->sortBy([
                ['test_type', 'asc'],
                ['dataset_size', 'asc'],
                ['method', 'asc'],
                ['scenario', 'asc'],
            ])
            ->values()
            ->toArray();
    }

    private function buatChartRows(array $summaryRows, string $testType): array
    {
        $rows = collect($summaryRows)
            ->where('test_type', $testType)
            ->where('clean_runs', '>', 0)
            ->groupBy(fn ($row) => $row['dataset_size'] . '|' . $row['method'])
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'dataset_size' => $first['dataset_size'],
                    'method' => $first['method'],
                    'mean_time_ms' => $items->avg('mean_time_ms'),
                ];
            })
            ->values();

        $labels = $rows->pluck('dataset_size')->unique()->sort()->values();
        $methods = $rows->pluck('method')->unique()->values();

        $datasets = $methods->map(function ($method) use ($rows, $labels) {
            return [
                'label' => $this->labelMethod($method),
                'method' => $method,
                'data' => $labels->map(function ($size) use ($rows, $method) {
                    $row = $rows->firstWhere(fn ($item) => $item['dataset_size'] === $size && $item['method'] === $method);

                    return $row ? (float) $row['mean_time_ms'] : null;
                })->values(),
            ];
        })->values();

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function buatChartMemoryRows(array $summaryRows): array
    {
        $rows = collect($summaryRows)
            ->where('clean_runs', '>', 0)
            ->groupBy(fn ($row) => $row['dataset_size'] . '|' . $row['method'])
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'dataset_size' => $first['dataset_size'],
                    'method' => $first['method'],
                    'mean_memory_kb' => $items->avg('mean_memory_kb'),
                ];
            })
            ->values();

        $labels = $rows->pluck('dataset_size')->unique()->sort()->values();

        $methodOrder = collect([
            'merge_sort_application',
            'mysql_order_by',
            'sequential_search_application',
            'mysql_where_like',
        ]);

        $methods = $methodOrder
            ->filter(fn ($method) => $rows->contains('method', $method))
            ->values();

        $datasets = $methods->map(function ($method) use ($rows, $labels) {
            return [
                'label' => $this->labelMethod($method),
                'method' => $method,
                'data' => $labels->map(function ($size) use ($rows, $method) {
                    $row = $rows->firstWhere(fn ($item) => $item['dataset_size'] === $size && $item['method'] === $method);

                    return $row ? (float) $row['mean_memory_kb'] : null;
                })->values(),
            ];
        })->values();

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function statistikIqr(Collection $values): array
    {
        $stats = $this->statistikDasar($values);

        if ($values->isEmpty()) {
            return array_merge($stats, [
                'q1' => 0,
                'q3' => 0,
                'iqr' => 0,
                'lower_bound' => 0,
                'upper_bound' => 0,
            ]);
        }

        $sorted = $values->sort()->values();

        $q1 = $this->percentile($sorted, 0.25);
        $q3 = $this->percentile($sorted, 0.75);
        $iqr = $q3 - $q1;

        return array_merge($stats, [
            'q1' => $q1,
            'q3' => $q3,
            'iqr' => $iqr,
            'lower_bound' => $q1 - (1.5 * $iqr),
            'upper_bound' => $q3 + (1.5 * $iqr),
        ]);
    }

    private function statistikDasar(Collection $values): array
    {
        $values = $values
            ->filter(fn ($value) => $value !== null && is_numeric($value))
            ->map(fn ($value) => (float) $value)
            ->values();

        if ($values->isEmpty()) {
            return [
                'mean' => 0,
                'median' => 0,
                'std_dev' => 0,
            ];
        }

        $mean = $values->avg();
        $median = $this->percentile($values->sort()->values(), 0.5);

        $variance = 0;

        if ($values->count() > 1) {
            $variance = $values
                ->map(fn ($value) => pow($value - $mean, 2))
                ->sum() / ($values->count() - 1);
        }

        return [
            'mean' => $mean,
            'median' => $median,
            'std_dev' => sqrt($variance),
        ];
    }

    private function percentile(Collection $sortedValues, float $percentile): float
    {
        $values = $sortedValues->sort()->values();
        $count = $values->count();

        if ($count === 0) {
            return 0;
        }

        if ($count === 1) {
            return (float) $values[0];
        }

        $index = ($count - 1) * $percentile;
        $lower = (int) floor($index);
        $upper = (int) ceil($index);

        if ($lower === $upper) {
            return (float) $values[$lower];
        }

        $weight = $index - $lower;

        return ((float) $values[$lower] * (1 - $weight)) + ((float) $values[$upper] * $weight);
    }

    private function waktuEksekusi(PerformanceLog $row): float
    {
        return (float) ($row->sort_time ?? $row->search_time ?? $row->baseline_time ?? 0);
    }

    private function tulisCsv(string $path, array $header, array $rows): void
    {
        $handle = fopen($path, 'w');

        fputcsv($handle, $header);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }

    private function labelMethod(string $method): string
    {
        return match ($method) {
            'merge_sort_application' => 'Merge Sort',
            'sequential_search_application' => 'Sequential Search',
            'mysql_order_by' => 'MySQL ORDER BY',
            'mysql_where_like' => 'MySQL WHERE/LIKE',
            default => $method,
        };
    }
}