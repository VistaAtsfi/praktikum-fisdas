<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_code',
        'timestamp',
        'dataset_size',
        'test_type',
        'method',
        'scenario',
        'field_name',
        'direction',
        'keyword',
        'run_order',
        'is_warmup',
        'is_outlier',
        'sort_time',
        'search_time',
        'baseline_time',
        'memory_usage',
        'iteration_count',
        'comparison_count',
        'result_count',
        'is_valid',
        'explain_result',
        'metadata',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'is_warmup' => 'boolean',
        'is_outlier' => 'boolean',
        'is_valid' => 'boolean',
        'sort_time' => 'float',
        'search_time' => 'float',
        'baseline_time' => 'float',
        'memory_usage' => 'float',
        'explain_result' => 'array',
        'metadata' => 'array',
    ];

    public function getExecutionTimeAttribute(): float
    {
        return (float) ($this->sort_time ?? $this->search_time ?? $this->baseline_time ?? 0);
    }
}