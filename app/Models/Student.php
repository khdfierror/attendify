<?php

namespace App\Models;

use App\Concerns\HasUlids;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'full_name',
        'nickname',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'email',
        'photo',
        'parent_name',
        'parent_phone',
        'parent_email',
        'parent_address',
        'class_id',
        'major_id',
        'enrollment_date',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
        'status' => StudentStatus::class,
    ];

    protected $appends = ['photo_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'class_id');
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendancePermissions(): HasMany
    {
        return $this->hasMany(AttendancePermission::class);
    }

    public function attendanceSummaries(): HasMany
    {
        return $this->hasMany(AttendanceSummary::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    // Accessors
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }

    // Helper Methods
    public function getAge(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    public function getAttendancePercentage($startDate = null, $endDate = null): float
    {
        $query = $this->attendances();

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $total = $query->count();

        if ($total === 0) {
            return 0;
        }

        $present = $query->whereIn('status', ['present', 'late'])->count();

        return round(($present / $total) * 100, 2);
    }
}
