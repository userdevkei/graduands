<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->string('cohort_id', 40);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('original_filename');
            $table->string('status', 20)->default('staged'); // staged | committed | cancelled
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('valid_rows')->default(0);
            $table->unsignedInteger('duplicate_rows')->default(0);
            $table->unsignedInteger('invalid_rows')->default(0);
            $table->timestamp('committed_at')->nullable();
            $table->timestamps();

            $table->foreign('cohort_id')->references('id')->on('cohorts')->cascadeOnDelete();
        });

        Schema::create('import_rows', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->string('import_batch_id', 40)->index();
            $table->unsignedInteger('row_number');            // row in the uploaded sheet (header = 1)
            $table->json('data');
            $table->string('status', 20)->index();            // valid | duplicate | invalid
            $table->json('errors')->nullable();
            $table->timestamps();

            $table->foreign('import_batch_id')->references('id')->on('import_batches')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_rows');
        Schema::dropIfExists('import_batches');
    }
};
