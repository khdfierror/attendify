<?php

namespace App\Models;

use App\Concerns\HasUlids;
use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'subject_id',
        'schedule_id',
        'academic_year_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
        'attachment',
        'created_by',
        'updated_by',
        'is_notified',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'is_notified' => 'boolean',
        'status' => AttendanceStatus::class,
    ];

    protected $appends = ['attachment_url'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    public function scopePresent($query)
    {
        return $query->whereIn('status', ['present', 'late']);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    // Accessors
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? Storage::url($this->attachment) : null;
    }

    // public function getStatusLabelAttribute(): string
    // {
    //     return match ($this->status) {
    //         'present' => 'Hadir',
    //         'late' => 'Terlambat',
    //         'sick' => 'Sakit',
    //         'permission' => 'Izin',
    //         'absent' => 'Alpa',
    //         default => 'Unknown',
    //     };
    // }

    // public function getStatusColorAttribute(): string
    // {
    //     return match ($this->status) {
    //         'present' => 'success',
    //         'late' => 'warning',
    //         'sick' => 'info',
    //         'permission' => 'primary',
    //         'absent' => 'danger',
    //         default => 'secondary',
    //     };
    // }

    // Static Helper untuk statistik
    public static function getStatsByClass($classId, $startDate = null, $endDate = null)
    {
        $query = static::where('class_id', $classId);

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        return [
            'total' => $query->count(),
            'present' => $query->where('status', 'present')->count(),
            'late' => $query->where('status', 'late')->count(),
            'sick' => $query->where('status', 'sick')->count(),
            'permission' => $query->where('status', 'permission')->count(),
            'absent' => $query->where('status', 'absent')->count(),
        ];
    }
}
