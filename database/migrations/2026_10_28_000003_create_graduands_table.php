<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduands', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->string('cohort_id', 40);
            $table->string('registration_number')->unique();
            $table->string('portal_first_name');          // as on student portal; used only for lookup, never edited

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('national_id_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();

            // read-only for students
            $table->string('programme');
            $table->string('award')->nullable();
            $table->string('classification')->nullable();

            $table->string('status', 20)->default('pending')->index(); // pending | confirmed | updated
            $table->json('original_data')->nullable();    // snapshot of editable fields before the student's one update
            $table->timestamp('submitted_at')->nullable();
            $table->string('submitted_ip', 45)->nullable();
            $table->timestamps();

            $table->foreign('cohort_id')->references('id')->on('cohorts')->restrictOnDelete();
            $table->index(['cohort_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduands');
    }
};
