<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'credit_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credit_hours' => 'integer',
    ];

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
}
