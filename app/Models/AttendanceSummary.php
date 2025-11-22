<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSummary extends Model
{
    use HasUlids;

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'period_type',
        'period',
        'total_days',
        'present_count',
        'late_count',
        'sick_count',
        'permission_count',
        'absent_count',
        'attendance_percentage',
    ];

    protected $casts = [
        'total_days' => 'integer',
        'present_count' => 'integer',
        'late_count' => 'integer',
        'sick_count' => 'integer',
        'permission_count' => 'integer',
        'absent_count' => 'integer',
        'attendance_percentage' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Scopes
    public function scopeMonthly($query)
    {
        return $query->where('period_type', 'monthly');
    }

    public function scopeSemester($query)
    {
        return $query->where('period_type', 'semester');
    }

    public function scopeYearly($query)
    {
        return $query->where('period_type', 'yearly');
    }

    // Helper untuk kalkulasi ulang
    public function recalculate()
    {
        $total = $this->present_count + $this->late_count + $this->sick_count +
                 $this->permission_count + $this->absent_count;

        $actualPresent = $this->present_count + $this->late_count;

        $percentage = $total > 0 ? ($actualPresent / $total) * 100 : 0;

        $this->update([
            'total_days' => $total,
            'attendance_percentage' => round($percentage, 2),
        ]);
    }
}
