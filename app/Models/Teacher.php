<?php

namespace App\Models;

use App\Concerns\HasUlids;
use App\Enums\TeacherStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Teacher extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nip',
        'full_name',
        'gender',
        'phone',
        'email',
        'address',
        'photo',
        'join_date',
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
        'status' => TeacherStatus::class,
    ];

    protected $appends = ['photo_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(Classroom::class, 'homeroom_teacher_id', 'user_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? Storage::url($this->photo) : null;
    }
}
