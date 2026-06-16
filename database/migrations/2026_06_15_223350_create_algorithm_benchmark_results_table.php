<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('algorithm_benchmark_results', function (Blueprint $table) {
            $table->id();

            $table->string('batch_code')->index();

            $table->unsignedInteger('dataset_size')->index();

            $table->string('test_type', 50)->index();
            $table->string('method', 80)->index();
            $table->string('scenario', 120)->index();

            $table->string('field_name', 80)->nullable();
            $table->string('direction', 20)->nullable();
            $table->string('keyword')->nullable();

            $table->unsignedInteger('run_order')->default(1);
            $table->boolean('is_warmup')->default(false)->index();

            $table->double('execution_time_ms')->default(0);
            $table->unsignedInteger('iteration_count')->default(0);
            $table->unsignedInteger('comparison_count')->default(0);
            $table->double('memory_usage_kb')->nullable();
            $table->unsignedInteger('result_count')->default(0);

            $table->boolean('is_valid')->default(true);
            $table->json('metadata')->nullable();

            $table->timestamp('tested_at')->nullable();

            $table->timestamps();

            $table->index(['dataset_size', 'test_type', 'method', 'scenario'], 'benchmark_group_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('algorithm_benchmark_results');
    }
};