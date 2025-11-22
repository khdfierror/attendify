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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->nullable(); // 2024/2025
            $table->year('start_year'); // 2024
            $table->year('end_year'); // 2025
            $table->date('start_date'); // Tanggal mulai semester
            $table->date('end_date'); // Tanggal akhir semester
            $table->boolean('is_active')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
