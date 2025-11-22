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
        Schema::create('classes', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name'); // X-A, XI-IPA-1
            $table->integer('grade_level'); // 10, 11, 12
            $table->foreignUlid('major_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('homeroom_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('max_students')->default(40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['academic_year_id', 'grade_level']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
