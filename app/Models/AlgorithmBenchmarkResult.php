<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlgorithmBenchmarkResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_code',
        'dataset_size',
        'test_type',
        'method',
        'scenario',
        'field_name',
        'direction',
        'keyword',
        'run_order',
        'is_warmup',
        'execution_time_ms',
        'iteration_count',
        'comparison_count',
        'memory_usage_kb',
        'result_count',
        'is_valid',
        'metadata',
        'tested_at',
    ];

    protected $casts = [
        'is_warmup' => 'boolean',
        'is_valid' => 'boolean',
        'metadata' => 'array',
        'tested_at' => 'datetime',
        'execution_time_ms' => 'float',
        'memory_usage_kb' => 'float',
    ];
}