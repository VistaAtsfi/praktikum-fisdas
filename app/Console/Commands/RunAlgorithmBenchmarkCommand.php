<?php

namespace App\Console\Commands;

use App\Models\LogSearch;
use App\Models\LogSorting;
use App\Models\PerformanceLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RunAlgorithmBenchmarkCommand extends Command
{
    protected $signature = 'benchmark:algoritma
        {--sizes=100,500,1000,5000 : Ukuran dataset, pisahkan dengan koma}
        {--runs=50 : Jumlah pengujian utama yang dihitung}
        {--warmup=10 : Jumlah warm-up yang tidak dihitung}
        {--fresh : Hapus data sintetis dan hasil benchmark lama}
        {--only=all : Pilihan: all, sort, search}';

    protected $description = 'Membuat data sintetis dan menjalankan pengujian Merge Sort, Sequential Search, serta baseline MySQL.';

    private array $idsCache = [];

    public function handle(): int
    {
        $sizes = collect(explode(',', (string) $this->option('sizes')))
            ->map(fn ($size) => (int) trim($size))
            ->filter(fn ($size) => $size > 0)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        if (empty($sizes)) {
            $this->error('Ukuran dataset tidak valid.');
            return self::FAILURE;
        }

        $runs = max(1, (int) $this->option('runs'));
        $warmup = max(0, (int) $this->option('warmup'));
        $only = strtolower((string) $this->option('only'));

        if (!in_array($only, ['all', 'sort', 'search'], true)) {
            $this->error('Option --only hanya boleh: all, sort, search.');
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->info('Menghapus hasil benchmark lama dan data sintetis lama...');
            $this->hapusHasilBenchmarkLama();
            $this->hapusUserSintetis();
        }

        $maxSize = max($sizes);

        $this->info("Menyiapkan data sintetis sebanyak {$maxSize} user...");
        $this->buatUserSintetis($maxSize);

        $batchCode = 'BENCH-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(5));

        $this->info("Batch pengujian: {$batchCode}");
        $this->info("Warm-up: {$warmup}, Pengujian utama: {$runs}");

        foreach ($sizes as $size) {
            $this->newLine();
            $this->info("Dataset {$size} data");

            if ($only === 'all' || $only === 'sort') {
                $this->ujiSorting($batchCode, $size, $warmup, $runs);
            }

            if ($only === 'all' || $only === 'search') {
                $this->ujiSearching($batchCode, $size, $warmup, $runs);
            }
        }

        $this->newLine();
        $this->info('Menandai outlier menggunakan metode IQR...');
        $this->tandaiOutlierIqr($batchCode);

        $this->info('Pengujian selesai.');
        $this->info('Buka halaman Evaluasi Algoritma untuk melihat grafik, statistik, dan export ZIP.');

        return self::SUCCESS;
    }

    private function hapusHasilBenchmarkLama(): void
    {
        PerformanceLog::query()->delete();

        LogSearch::where('keterangan_pengujian', 'like', '%benchmark_sintetis%')->delete();
        LogSorting::where('keterangan_pengujian', 'like', '%benchmark_sintetis%')->delete();
    }

    private function hapusUserSintetis(): void
    {
        User::where('email', 'like', 'synthetic.praktikan.%@benchmark.test')
            ->orWhere('nim', 'like', 'BMK%')
            ->delete();
    }

    private function buatUserSintetis(int $total): void
    {
        $jumlahSekarang = User::where('email', 'like', 'synthetic.praktikan.%@benchmark.test')->count();

        if ($jumlahSekarang === $total) {
            $this->info("Data sintetis sudah tersedia: {$jumlahSekarang} user.");
            return;
        }

        $this->hapusUserSintetis();

        $prodis = [
            'Teknik Elektro',
            'Teknik Geologi',
            'Teknik Industri',
            'Teknik Lingkungan',
            'Teknik Pertambangan',
        ];

        $namaDepan = [
            'Ahmad', 'Muhammad', 'Aulia', 'Nadia', 'Fajar',
            'Dinda', 'Rizky', 'Andi', 'Salsa', 'Bagus',
            'Rafi', 'Nur', 'Yusuf', 'Citra', 'Putri',
            'Dewi', 'Ardi', 'Naufal', 'Zahra', 'Rama',
        ];

        $namaBelakang = [
            'Pratama', 'Ramadhani', 'Saputra', 'Azzahra', 'Maulana',
            'Putra', 'Lestari', 'Wijaya', 'Ananda', 'Permata',
            'Hidayat', 'Fadhilah', 'Rahman', 'Kusuma', 'Nugroho',
            'Sari', 'Syahputra', 'Purnomo', 'Setiawan', 'Mahardika',
        ];

        $password = Hash::make('password');
        $now = now();
        $rows = [];

        for ($i = 1; $i <= $total; $i++) {
            $nomor = str_pad((string) $i, 6, '0', STR_PAD_LEFT);
            $nim = 'BMK' . str_pad((string) $i, 9, '0', STR_PAD_LEFT);

            $name = $i % 25 === 0
                ? 'Nama Duplikat Benchmark'
                : $namaDepan[$i % count($namaDepan)] . ' ' . $namaBelakang[$i % count($namaBelakang)] . ' ' . $nomor;

            $rows[] = [
                'name' => $name,
                'email' => 'synthetic.praktikan.' . $nomor . '@benchmark.test',
                'email_verified_at' => $now,
                'password' => $password,
                'role' => 'praktikan',
                'nim' => $nim,
                'prodi' => $prodis[$i % count($prodis)],
                'jenis_kelamin' => $i % 2 === 0 ? 'L' : 'P',
                'nomor_ponsel' => '08' . str_pad((string) $i, 10, '0', STR_PAD_LEFT),
                'auth_provider' => 'email',
                'profile_completed' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= 500) {
                DB::table('users')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('users')->insert($rows);
        }

        $this->info("Data sintetis berhasil dibuat: {$total} user.");
    }

    private function ujiSorting(string $batchCode, int $size, int $warmup, int $runs): void
    {
        $scenarios = [
            ['name' => 'random_ascending', 'direction' => 'asc', 'prepare' => 'random'],
            ['name' => 'random_descending', 'direction' => 'desc', 'prepare' => 'random'],
            ['name' => 'already_sorted', 'direction' => 'asc', 'prepare' => 'sorted'],
            ['name' => 'duplicate_value', 'direction' => 'asc', 'prepare' => 'random'],
        ];

        foreach ($scenarios as $scenario) {
            $totalRun = $warmup + $runs;

            for ($run = 1; $run <= $totalRun; $run++) {
                $isWarmup = $run <= $warmup;
                $runOrder = $isWarmup ? $run : $run - $warmup;

                $dataset = $this->datasetArray($size, $scenario['prepare']);

                $hasilMergeSort = $this->ukur(function () use ($dataset, $scenario) {
                    return $this->mergeSortPraktikan($dataset, 'prodi_nim', $scenario['direction']);
                });

                $payloadMerge = $hasilMergeSort['payload'];

                $this->simpanHasil(
                    batchCode: $batchCode,
                    datasetSize: $size,
                    testType: 'sorting',
                    method: 'merge_sort_application',
                    scenario: $scenario['name'],
                    fieldName: 'prodi_nim',
                    direction: $scenario['direction'],
                    keyword: null,
                    runOrder: $runOrder,
                    isWarmup: $isWarmup,
                    executionTimeMs: $hasilMergeSort['execution_time_ms'],
                    iterationCount: 0,
                    comparisonCount: $payloadMerge['comparison_count'],
                    memoryUsageKb: $hasilMergeSort['memory_usage_kb'],
                    resultCount: $payloadMerge['result_count'],
                    isValid: $payloadMerge['is_valid'],
                    metadata: [
                        'baseline' => false,
                        'data_source' => 'synthetic_users',
                    ]
                );

                $query = $this->mysqlOrderByQuery($size, $scenario['direction']);

                $hasilMysql = $this->ukur(function () use ($query, $scenario) {
                    $rows = collect(DB::select($query['sql'], $query['params']))
                        ->map(fn ($row) => (array) $row)
                        ->values()
                        ->all();

                    return [
                        'result_count' => count($rows),
                        'is_valid' => $this->cekTerurut($rows, 'prodi_nim', $scenario['direction']),
                    ];
                });

                $payloadMysql = $hasilMysql['payload'];
                $explain = DB::select('EXPLAIN ' . $query['sql'], $query['params']);

                $this->simpanHasil(
                    batchCode: $batchCode,
                    datasetSize: $size,
                    testType: 'sorting',
                    method: 'mysql_order_by',
                    scenario: $scenario['name'],
                    fieldName: 'prodi_nim',
                    direction: $scenario['direction'],
                    keyword: null,
                    runOrder: $runOrder,
                    isWarmup: $isWarmup,
                    executionTimeMs: $hasilMysql['execution_time_ms'],
                    iterationCount: 0,
                    comparisonCount: 0,
                    memoryUsageKb: $hasilMysql['memory_usage_kb'],
                    resultCount: $payloadMysql['result_count'],
                    isValid: $payloadMysql['is_valid'],
                    metadata: [
                        'baseline' => true,
                        'sql' => $query['sql'],
                        'explain' => $explain,
                    ]
                );
            }

            $this->info("Sorting {$scenario['name']} selesai untuk {$size} data.");
        }
    }

    private function ujiSearching(string $batchCode, int $size, int $warmup, int $runs): void
    {
        $datasetAwal = collect($this->datasetArray($size, 'sorted'));

        $targetAda = $datasetAwal->last();
        $targetDuplikat = $datasetAwal->firstWhere('name', 'Nama Duplikat Benchmark');

        $scenarios = [
            [
                'name' => 'keyword_found',
                'keyword' => $targetAda['nim'] ?? 'BMK000000001',
                'field' => 'nim_name',
                'expected' => 'found',
            ],
            [
                'name' => 'keyword_not_found',
                'keyword' => 'DATA_TIDAK_ADA_000',
                'field' => 'nim_name',
                'expected' => 'not_found',
            ],
            [
                'name' => 'keyword_empty',
                'keyword' => '',
                'field' => 'nim_name',
                'expected' => 'all',
            ],
            [
                'name' => 'duplicate_keyword',
                'keyword' => $targetDuplikat ? 'Nama Duplikat Benchmark' : 'Duplikat',
                'field' => 'nim_name',
                'expected' => 'duplicate',
            ],
        ];

        foreach ($scenarios as $scenario) {
            $totalRun = $warmup + $runs;

            for ($run = 1; $run <= $totalRun; $run++) {
                $isWarmup = $run <= $warmup;
                $runOrder = $isWarmup ? $run : $run - $warmup;

                $dataset = $this->datasetArray($size, 'sorted');

                $hasilSequential = $this->ukur(function () use ($dataset, $scenario) {
                    return $this->sequentialSearchPraktikan($dataset, $scenario['keyword'], $scenario['expected']);
                });

                $payloadSequential = $hasilSequential['payload'];

                $this->simpanHasil(
                    batchCode: $batchCode,
                    datasetSize: $size,
                    testType: 'searching',
                    method: 'sequential_search_application',
                    scenario: $scenario['name'],
                    fieldName: $scenario['field'],
                    direction: null,
                    keyword: $scenario['keyword'],
                    runOrder: $runOrder,
                    isWarmup: $isWarmup,
                    executionTimeMs: $hasilSequential['execution_time_ms'],
                    iterationCount: $payloadSequential['iteration_count'],
                    comparisonCount: $payloadSequential['comparison_count'],
                    memoryUsageKb: $hasilSequential['memory_usage_kb'],
                    resultCount: $payloadSequential['result_count'],
                    isValid: $payloadSequential['is_valid'],
                    metadata: [
                        'baseline' => false,
                        'data_source' => 'synthetic_users',
                    ]
                );

                $query = $this->mysqlWhereLikeQuery($size, $scenario['keyword']);

                $hasilMysql = $this->ukur(function () use ($query, $scenario, $size) {
                    $rows = DB::select($query['sql'], $query['params']);

                    return [
                        'result_count' => count($rows),
                        'is_valid' => $this->validasiHasilSearch($scenario['expected'], count($rows), $size),
                    ];
                });

                $payloadMysql = $hasilMysql['payload'];
                $explain = DB::select('EXPLAIN ' . $query['sql'], $query['params']);

                $this->simpanHasil(
                    batchCode: $batchCode,
                    datasetSize: $size,
                    testType: 'searching',
                    method: 'mysql_where_like',
                    scenario: $scenario['name'],
                    fieldName: $scenario['field'],
                    direction: null,
                    keyword: $scenario['keyword'],
                    runOrder: $runOrder,
                    isWarmup: $isWarmup,
                    executionTimeMs: $hasilMysql['execution_time_ms'],
                    iterationCount: 0,
                    comparisonCount: 0,
                    memoryUsageKb: $hasilMysql['memory_usage_kb'],
                    resultCount: $payloadMysql['result_count'],
                    isValid: $payloadMysql['is_valid'],
                    metadata: [
                        'baseline' => true,
                        'sql' => $query['sql'],
                        'explain' => $explain,
                    ]
                );
            }

            $this->info("Searching {$scenario['name']} selesai untuk {$size} data.");
        }
    }

    private function datasetIds(int $size): array
    {
        if (isset($this->idsCache[$size])) {
            return $this->idsCache[$size];
        }

        $this->idsCache[$size] = DB::table('users')
            ->where('email', 'like', 'synthetic.praktikan.%@benchmark.test')
            ->orderBy('nim')
            ->limit($size)
            ->pluck('id')
            ->toArray();

        return $this->idsCache[$size];
    }

    private function datasetArray(int $size, string $mode = 'sorted'): array
    {
        $ids = $this->datasetIds($size);

        $items = DB::table('users')
            ->select('id', 'name', 'email', 'nim', 'prodi', 'jenis_kelamin')
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->values();

        if ($mode === 'random') {
            return $items->shuffle()->values()->all();
        }

        if ($mode === 'sorted') {
            return $items
                ->sortBy(fn ($user) => [
                    strtolower((string) $user['prodi']),
                    (int) substr((string) $user['nim'], -3),
                ])
                ->values()
                ->all();
        }

        return $items->all();
    }

    private function ukur(callable $callback): array
    {
        gc_collect_cycles();

        if (function_exists('memory_reset_peak_usage')) {
            memory_reset_peak_usage();
        }

        /*
        |--------------------------------------------------------------------------
        | Pengukuran sesuai BAB 3
        |--------------------------------------------------------------------------
        | Waktu:
        | - Menggunakan microtime(true)
        | - Selisih waktu dikonversi ke milidetik
        |
        | Memori:
        | - Peak memory tetap dicatat dengan memory_get_peak_usage(true)
        | - Untuk grafik, yang dipakai adalah kenaikan memori selama proses berjalan
        |   supaya hasil antar metode tidak terlihat sama semua akibat baseline Laravel.
        */
        $memoryStartReal = memory_get_usage(false);
        $memoryStartAllocated = memory_get_usage(true);

        $timeStart = microtime(true);

        $payload = $callback();

        $timeEnd = microtime(true);

        $memoryPeakReal = memory_get_peak_usage(false);
        $memoryPeakAllocated = memory_get_peak_usage(true);

        $memoryDeltaRealKb = max(0, $memoryPeakReal - $memoryStartReal) / 1024;
        $memoryDeltaAllocatedKb = max(0, $memoryPeakAllocated - $memoryStartAllocated) / 1024;
        $memoryPeakAllocatedKb = $memoryPeakAllocated / 1024;

        return [
            'execution_time_ms' => ($timeEnd - $timeStart) * 1000,

            /*
            |--------------------------------------------------------------------------
            | Nilai utama untuk grafik/tabel
            |--------------------------------------------------------------------------
            | Pakai delta real memory supaya perbedaan metode terlihat.
            */
            'memory_usage_kb' => $memoryDeltaRealKb,

            /*
            |--------------------------------------------------------------------------
            | Nilai tambahan untuk metadata/export bila dibutuhkan
            |--------------------------------------------------------------------------
            */
            'memory_delta_allocated_kb' => $memoryDeltaAllocatedKb,
            'memory_peak_usage_true_kb' => $memoryPeakAllocatedKb,

            'payload' => $payload,
        ];
    }

    private function mergeSortPraktikan(array $items, string $field, string $direction): array
    {
        $comparisonCount = 0;

        $sorted = $this->mergeSortArray($items, $field, $direction, $comparisonCount);

        return [
            'result_count' => count($sorted),
            'comparison_count' => $comparisonCount,
            'is_valid' => $this->cekTerurut($sorted, $field, $direction),
        ];
    }

    private function mergeSortArray(array $items, string $field, string $direction, int &$comparisonCount): array
    {
        $count = count($items);

        if ($count <= 1) {
            return $items;
        }

        $middle = intdiv($count, 2);

        $left = array_slice($items, 0, $middle);
        $right = array_slice($items, $middle);

        $left = $this->mergeSortArray($left, $field, $direction, $comparisonCount);
        $right = $this->mergeSortArray($right, $field, $direction, $comparisonCount);

        return $this->mergeArray($left, $right, $field, $direction, $comparisonCount);
    }

    private function mergeArray(array $left, array $right, string $field, string $direction, int &$comparisonCount): array
    {
        $result = [];
        $i = 0;
        $j = 0;

        $leftCount = count($left);
        $rightCount = count($right);

        while ($i < $leftCount && $j < $rightCount) {
            $comparisonCount++;

            if ($this->ambilKiriUntukSort($left[$i], $right[$j], $field, $direction)) {
                $result[] = $left[$i];
                $i++;
            } else {
                $result[] = $right[$j];
                $j++;
            }
        }

        while ($i < $leftCount) {
            $result[] = $left[$i];
            $i++;
        }

        while ($j < $rightCount) {
            $result[] = $right[$j];
            $j++;
        }

        return $result;
    }

    private function ambilKiriUntukSort(array $left, array $right, string $field, string $direction): bool
    {
        if ($field === 'prodi_nim') {
            $prodiLeft = strtolower((string) ($left['prodi'] ?? ''));
            $prodiRight = strtolower((string) ($right['prodi'] ?? ''));

            if ($prodiLeft !== $prodiRight) {
                return $prodiLeft <= $prodiRight;
            }

            $nimLeft = (int) substr((string) ($left['nim'] ?? ''), -3);
            $nimRight = (int) substr((string) ($right['nim'] ?? ''), -3);

            if ($nimLeft === $nimRight) {
                return true;
            }

            return $direction === 'desc'
                ? $nimLeft >= $nimRight
                : $nimLeft <= $nimRight;
        }

        $valueLeft = strtolower((string) ($left[$field] ?? ''));
        $valueRight = strtolower((string) ($right[$field] ?? ''));

        if ($valueLeft === $valueRight) {
            return true;
        }

        return $direction === 'desc'
            ? $valueLeft >= $valueRight
            : $valueLeft <= $valueRight;
    }

    private function cekTerurut(array $items, string $field, string $direction): bool
    {
        for ($i = 1; $i < count($items); $i++) {
            if (!$this->ambilKiriUntukSort($items[$i - 1], $items[$i], $field, $direction)) {
                return false;
            }
        }

        return true;
    }

    private function sequentialSearchPraktikan(array $items, string $keyword, string $expected): array
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return [
                'result_count' => count($items),
                'iteration_count' => 0,
                'comparison_count' => 0,
                'is_valid' => $expected === 'all',
            ];
        }

        $resultCount = 0;
        $iterationCount = 0;
        $comparisonCount = 0;
        $key = strtolower($keyword);

        foreach ($items as $item) {
            $iterationCount++;

            $nim = strtolower((string) ($item['nim'] ?? ''));
            $nama = strtolower((string) ($item['name'] ?? ''));

            $comparisonCount++;

            if (str_contains($nim, $key) || str_contains($nama, $key)) {
                $resultCount++;
            }
        }

        return [
            'result_count' => $resultCount,
            'iteration_count' => $iterationCount,
            'comparison_count' => $comparisonCount,
            'is_valid' => $this->validasiHasilSearch($expected, $resultCount, count($items)),
        ];
    }

    private function mysqlOrderByQuery(int $size, string $direction): array
    {
        $directionSql = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';

        return [
            'sql' => "
                SELECT id, name, email, nim, prodi, jenis_kelamin
                FROM (
                    SELECT id, name, email, nim, prodi, jenis_kelamin
                    FROM users
                    WHERE email LIKE ?
                    ORDER BY nim ASC
                    LIMIT {$size}
                ) AS dataset
                ORDER BY prodi ASC, CAST(RIGHT(nim, 3) AS UNSIGNED) {$directionSql}
            ",
            'params' => ['synthetic.praktikan.%@benchmark.test'],
        ];
    }

    private function mysqlWhereLikeQuery(int $size, string $keyword): array
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return [
                'sql' => "
                    SELECT id, name, email, nim, prodi, jenis_kelamin
                    FROM (
                        SELECT id, name, email, nim, prodi, jenis_kelamin
                        FROM users
                        WHERE email LIKE ?
                        ORDER BY nim ASC
                        LIMIT {$size}
                    ) AS dataset
                ",
                'params' => ['synthetic.praktikan.%@benchmark.test'],
            ];
        }

        return [
            'sql' => "
                SELECT id, name, email, nim, prodi, jenis_kelamin
                FROM (
                    SELECT id, name, email, nim, prodi, jenis_kelamin
                    FROM users
                    WHERE email LIKE ?
                    ORDER BY nim ASC
                    LIMIT {$size}
                ) AS dataset
                WHERE nim LIKE ? OR name LIKE ?
            ",
            'params' => [
                'synthetic.praktikan.%@benchmark.test',
                '%' . $keyword . '%',
                '%' . $keyword . '%',
            ],
        ];
    }

    private function validasiHasilSearch(string $expected, int $resultCount, int $totalData): bool
    {
        return match ($expected) {
            'found' => $resultCount >= 1,
            'not_found' => $resultCount === 0,
            'all' => $resultCount === $totalData,
            'duplicate' => $resultCount >= 1,
            default => true,
        };
    }

    private function simpanHasil(
        string $batchCode,
        int $datasetSize,
        string $testType,
        string $method,
        string $scenario,
        ?string $fieldName,
        ?string $direction,
        ?string $keyword,
        int $runOrder,
        bool $isWarmup,
        float $executionTimeMs,
        int $iterationCount,
        int $comparisonCount,
        ?float $memoryUsageKb,
        int $resultCount,
        bool $isValid,
        array $metadata = []
    ): void {
        $isBaseline = str_starts_with($method, 'mysql_');

        PerformanceLog::create([
            'batch_code' => $batchCode,
            'timestamp' => now(),
            'dataset_size' => $datasetSize,
            'test_type' => $testType,
            'method' => $method,
            'scenario' => $scenario,
            'field_name' => $fieldName,
            'direction' => $direction,
            'keyword' => $keyword,
            'run_order' => $runOrder,
            'is_warmup' => $isWarmup,
            'is_outlier' => false,
            'sort_time' => $testType === 'sorting' && !$isBaseline ? $executionTimeMs : null,
            'search_time' => $testType === 'searching' && !$isBaseline ? $executionTimeMs : null,
            'baseline_time' => $isBaseline ? $executionTimeMs : null,
            'memory_usage' => $memoryUsageKb,
            'iteration_count' => $iterationCount,
            'comparison_count' => $comparisonCount,
            'result_count' => $resultCount,
            'is_valid' => $isValid,
            'explain_result' => $metadata['explain'] ?? null,
            'metadata' => $metadata,
        ]);

        if ($isWarmup) {
            return;
        }

        if ($testType === 'sorting') {
            LogSorting::create([
                'jumlah_data' => $datasetSize,
                'field_pengurutan' => $fieldName ?? '-',
                'arah_pengurutan' => $direction ?? '-',
                'waktu_pemrosesan_server' => $executionTimeMs,
                'jumlah_perbandingan' => $comparisonCount,
                'penggunaan_memori' => $memoryUsageKb,
                'waktu_pengujian' => now(),
                'keterangan_pengujian' => 'benchmark_sintetis | method=' . $method . ' | scenario=' . $scenario . ' | batch=' . $batchCode,
            ]);
        }

        if ($testType === 'searching') {
            LogSearch::create([
                'jumlah_data' => $datasetSize,
                'field_pencarian' => $fieldName ?? '-',
                'kata_kunci_pencarian' => $keyword ?? '',
                'waktu_pemrosesan_server' => $executionTimeMs,
                'jumlah_iterasi' => $iterationCount,
                'jumlah_perbandingan' => $comparisonCount,
                'penggunaan_memori' => $memoryUsageKb,
                'waktu_pengujian' => now(),
                'keterangan_pengujian' => 'benchmark_sintetis | method=' . $method . ' | scenario=' . $scenario . ' | batch=' . $batchCode,
            ]);
        }
    }

    private function tandaiOutlierIqr(string $batchCode): void
    {
        PerformanceLog::where('batch_code', $batchCode)
            ->where('is_warmup', false)
            ->update(['is_outlier' => false]);

        $groups = PerformanceLog::where('batch_code', $batchCode)
            ->where('is_warmup', false)
            ->get()
            ->groupBy(function ($row) {
                return implode('|', [
                    $row->dataset_size,
                    $row->test_type,
                    $row->method,
                    $row->scenario,
                ]);
            });

        foreach ($groups as $items) {
            $values = $items
                ->map(fn ($item) => $this->waktuEksekusiLog($item))
                ->sort()
                ->values();

            if ($values->count() < 4) {
                continue;
            }

            $q1 = $this->percentile($values, 0.25);
            $q3 = $this->percentile($values, 0.75);
            $iqr = $q3 - $q1;

            $lowerBound = $q1 - (1.5 * $iqr);
            $upperBound = $q3 + (1.5 * $iqr);

            $outlierIds = $items
                ->filter(function ($item) use ($lowerBound, $upperBound) {
                    $time = $this->waktuEksekusiLog($item);

                    return $time < $lowerBound || $time > $upperBound;
                })
                ->pluck('id')
                ->values();

            if ($outlierIds->isNotEmpty()) {
                PerformanceLog::whereIn('id', $outlierIds)->update([
                    'is_outlier' => true,
                ]);
            }
        }
    }

    private function waktuEksekusiLog(PerformanceLog $log): float
    {
        return (float) ($log->sort_time ?? $log->search_time ?? $log->baseline_time ?? 0);
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
}