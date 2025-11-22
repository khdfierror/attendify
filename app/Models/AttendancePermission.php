<?php

namespace App\Models;

use App\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class AttendancePermission extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'student_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'submitted_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['attachment_url', 'duration_days', 'type_label', 'status_label'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->where('status', 'approved');
    }

    // Accessors
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? Storage::url($this->attachment) : null;
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'sick' => 'Sakit',
            'permission' => 'Izin',
            'other' => 'Lainnya',
            default => 'Unknown',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    // Helper Methods
    public function approve($userId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    public function reject($userId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }
}
