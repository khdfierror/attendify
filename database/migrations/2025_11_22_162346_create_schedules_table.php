<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('class_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('day')->nullable(); // ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable(); // Ruang kelas
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['class_id', 'day', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
