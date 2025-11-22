<?php

namespace App\Models;

use App\Concerns\HasUlids;
use App\Enums\Kelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use HasUlids, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade_level',
        'major_id',
        'academic_year_id',
        'homeroom_teacher_id',
        'max_students',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'grade_level' => 'integer',
        'max_students' => 'integer',
        'grade_level' => Kelas::class,
    ];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGradeLevel($query, $level)
    {
        return $query->where('grade_level', $level);
    }

    // Helper untuk cek kapasitas
    public function isFull(): bool
    {
        return $this->students()->count() >= $this->max_students;
    }

    public function availableSeats(): int
    {
        return $this->max_students - $this->students()->count();
    }
}
