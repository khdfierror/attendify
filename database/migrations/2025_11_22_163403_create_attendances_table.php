<?php

// database/migrations/2024_01_01_000008_create_attendances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('attendances', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('class_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('academic_year_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->string('status')->nullable(); // ['present', 'late', 'sick', 'permission', 'absent']
            // present = hadir, late = terlambat, sick = sakit, permission = izin, absent = alpa
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable(); // Surat keterangan
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_notified')->default(false); // Sudah kirim notifikasi ke ortu?
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'date']);
            $table->index(['class_id', 'date']);
            $table->index(['date', 'status']);
            $table->unique(['student_id', 'date', 'subject_id'], 'unique_attendance');
        });

        Schema::create('attendance_permissions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('type')->nullable(); // ['sick', 'permission', 'other']
            $table->text('reason');
            $table->string('attachment')->nullable(); // File surat keterangan
            $table->string('status')->nullable(); // ['sick', 'permission', 'other']
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignUlid('submitted_by')->nullable()->constrained('users')->nullOnDelete(); // Bisa siswa atau ortu
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });

        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('student_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('class_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('period_type')->nullable(); // ['monthly', 'semester', 'yearly']
            $table->string('period'); // 2024-01, 2024-semester-1, 2024
            $table->integer('total_days'); // Total hari efektif
            $table->integer('present_count')->default(0);
            $table->integer('late_count')->default(0);
            $table->integer('sick_count')->default(0);
            $table->integer('permission_count')->default(0);
            $table->integer('absent_count')->default(0);
            $table->decimal('attendance_percentage', 5, 2)->default(0); // 95.50%
            $table->timestamps();

            $table->index(['student_id', 'period_type', 'period']);
            $table->unique(['student_id', 'period_type', 'period'], 'unique_summary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_permissions');
        Schema::dropIfExists('attendance_summaries');
    }
};
