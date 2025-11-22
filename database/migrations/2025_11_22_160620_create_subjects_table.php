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
        Schema::create('subjects', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code')->unique(); // MAT01, BIO02
            $table->string('name'); // Matematika, Biologi
            $table->text('description')->nullable();
            $table->integer('credit_hours')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
