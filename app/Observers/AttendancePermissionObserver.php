<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\AttendancePermission;

class AttendancePermissionObserver
{
    public function updated(AttendancePermission $permission): void
    {
        // Ketika surat izin disetujui, update attendance records
        if ($permission->isDirty('status') && $permission->status === 'approved') {
            $this->createAttendanceRecords($permission);
        }
    }

    protected function createAttendanceRecords(AttendancePermission $permission): void
    {
        $startDate = $permission->start_date;
        $endDate = $permission->end_date;
        $student = $permission->student;

        // Loop setiap hari dalam range
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            // Skip weekend (opsional, sesuaikan dengan kebijakan sekolah)
            if ($date->isWeekend()) {
                continue;
            }

            // Cek apakah sudah ada attendance record
            $exists = Attendance::where('student_id', $student->id)
                ->whereDate('date', $date)
                ->exists();

            if (! $exists) {
                Attendance::create([
                    'student_id' => $student->id,
                    'class_id' => $student->class_id,
                    'academic_year_id' => $student->class->academic_year_id,
                    'date' => $date,
                    'status' => $permission->type === 'sick' ? 'sick' : 'permission',
                    'notes' => $permission->reason,
                    'attachment' => $permission->attachment,
                    'created_by' => $permission->approved_by,
                    'is_notified' => true, // Sudah ada surat izin
                ]);
            }
        }
    }
}
