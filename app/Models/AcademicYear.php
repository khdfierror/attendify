<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasUlids, SoftDeletes;

    protected $table = 'academic_years';

    protected $fillable = [
        'name',
        'start_year',
        'end_year',
        'start_date',
        'end_date',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Hanya boleh ada 1 tahun ajaran aktif
    protected static function booted()
    {
        static::saving(function ($academicYear) {
            if ($academicYear->is_active) {
                static::where('id', '!=', $academicYear->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceSummaries(): HasMany
    {
        return $this->hasMany(AttendanceSummary::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
