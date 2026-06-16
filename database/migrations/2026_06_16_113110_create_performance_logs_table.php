<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_logs', function (Blueprint $table) {
            $table->id();

            $table->string('batch_code')->index();

            $table->timestamp('timestamp')->nullable()->index();
            $table->unsignedInteger('dataset_size')->index();

            $table->string('test_type', 50)->index(); 
            $table->string('method', 80)->index();
            $table->string('scenario', 120)->index();

            $table->string('field_name', 80)->nullable();
            $table->string('direction', 20)->nullable();
            $table->string('keyword')->nullable();

            $table->unsignedInteger('run_order')->default(1);
            $table->boolean('is_warmup')->default(false)->index();
            $table->boolean('is_outlier')->default(false)->index();

            $table->double('sort_time')->nullable();
            $table->double('search_time')->nullable();
            $table->double('baseline_time')->nullable();

            $table->double('memory_usage')->nullable();

            $table->unsignedInteger('iteration_count')->default(0);
            $table->unsignedInteger('comparison_count')->default(0);
            $table->unsignedInteger('result_count')->default(0);

            $table->boolean('is_valid')->default(true);

            $table->json('explain_result')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(
                ['dataset_size', 'test_type', 'method', 'scenario'],
                'performance_group_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_logs');
    }
};