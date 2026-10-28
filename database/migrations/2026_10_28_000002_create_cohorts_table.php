<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->string('name');
            $table->unsignedSmallInteger('graduation_year');
            $table->dateTime('opens_at')->nullable();   // confirmation window start
            $table->dateTime('closes_at')->nullable();  // confirmation window end (records lock after this)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};
