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
        Schema::create('students', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nis')->unique(); // Nomor Induk Siswa
            $table->string('nisn')->unique()->nullable(); // Nomor Induk Siswa Nasional
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->enum('gender', ['L', 'P']); // Laki-laki, Perempuan
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('photo')->nullable();

            // Data Orang Tua/Wali
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('parent_email')->nullable();
            $table->text('parent_address')->nullable();

            // Data Akademik
            $table->foreignUlid('class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('major_id')->nullable()->constrained()->nullOnDelete();
            $table->date('enrollment_date')->nullable(); // Tanggal masuk
            $table->string('status')->nullable(); // 'active', 'inactive', 'graduated', 'transferred', 'dropped_out'

            $table->timestamps();
            $table->softDeletes();

            $table->index(['nis', 'status']);
            $table->index('class_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
