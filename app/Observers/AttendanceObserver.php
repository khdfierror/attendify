<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\AttendanceSummary;
use App\Notifications\StudentAbsentNotification;
use Illuminate\Support\Facades\Notification;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        // Kirim notifikasi jika status absent/alpa dan belum ternotifikasi
        if ($attendance->status === 'absent' && ! $attendance->is_notified) {
            $this->sendParentNotification($attendance);
        }

        // Update summary
        $this->updateSummary($attendance);
    }

    public function updated(Attendance $attendance): void
    {
        // Cek jika status berubah menjadi absent
        if ($attendance->isDirty('status') && $attendance->status === 'absent' && ! $attendance->is_notified) {
            $this->sendParentNotification($attendance);
        }

        // Update summary
        $this->updateSummary($attendance);
    }

    public function deleted(Attendance $attendance): void
    {
        // Update summary saat data dihapus
        $this->updateSummary($attendance);
    }

    protected function sendParentNotification(Attendance $attendance): void
    {
        $student = $attendance->student;

        // Kirim ke parent phone jika ada
        if ($student->parent_phone) {
            // TODO: Implement WhatsApp/SMS notification
            // Contoh: WhatsAppService::send($student->parent_phone, $message);
        }

        // Kirim ke parent email jika ada
        if ($student->parent_email) {
            // TODO: Implement Email notification
            // Notification::route('mail', $student->parent_email)
            //     ->notify(new StudentAbsentNotification($attendance));
        }

        // Mark as notified
        $attendance->update(['is_notified' => true]);
    }

    protected function updateSummary(Attendance $attendance): void
    {
        $period = $attendance->date->format('Y-m');

        AttendanceSummary::updateOrCreate(
            [
                'student_id' => $attendance->student_id,
                'class_id' => $attendance->class_id,
                'academic_year_id' => $attendance->academic_year_id,
                'period_type' => 'monthly',
                'period' => $period,
            ],
            [
                'present_count' => Attendance::byStudent($attendance->student_id)
                    ->where('status', 'present')
                    ->whereYear('date', $attendance->date->year)
                    ->whereMonth('date', $attendance->date->month)
                    ->count(),
                'late_count' => Attendance::byStudent($attendance->student_id)
                    ->where('status', 'late')
                    ->whereYear('date', $attendance->date->year)
                    ->whereMonth('date', $attendance->date->month)
                    ->count(),
                'sick_count' => Attendance::byStudent($attendance->student_id)
                    ->where('status', 'sick')
                    ->whereYear('date', $attendance->date->year)
                    ->whereMonth('date', $attendance->date->month)
                    ->count(),
                'permission_count' => Attendance::byStudent($attendance->student_id)
                    ->where('status', 'permission')
                    ->whereYear('date', $attendance->date->year)
                    ->whereMonth('date', $attendance->date->month)
                    ->count(),
                'absent_count' => Attendance::byStudent($attendance->student_id)
                    ->where('status', 'absent')
                    ->whereYear('date', $attendance->date->year)
                    ->whereMonth('date', $attendance->date->month)
                    ->count(),
            ]
        );

        // Recalculate percentage
        $summary = AttendanceSummary::where('student_id', $attendance->student_id)
            ->where('period_type', 'monthly')
            ->where('period', $period)
            ->first();

        if ($summary) {
            $summary->recalculate();
        }
    }
}
