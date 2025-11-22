<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Bulk create attendance untuk seluruh siswa dalam kelas
     */
    public function bulkCreateForClass(ClassRoom $class, Carbon $date, array $attendanceData): array
    {
        $created = [];
        $errors = [];

        foreach ($attendanceData as $studentId => $data) {
            try {
                $attendance = Attendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'class_id' => $class->id,
                        'date' => $date,
                        'subject_id' => $data['subject_id'] ?? null,
                    ],
                    [
                        'academic_year_id' => $class->academic_year_id,
                        'status' => $data['status'] ?? 'present',
                        'check_in_time' => $data['check_in_time'] ?? now(),
                        'notes' => $data['notes'] ?? null,
                        'created_by' => auth()->id(),
                    ]
                );

                $created[] = $attendance;
            } catch (\Exception $e) {
                $errors[$studentId] = $e->getMessage();
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
        ];
    }

    /**
     * Generate attendance report
     */
    public function generateReport($studentId, $startDate, $endDate)
    {
        $attendances = Attendance::byStudent($studentId)
            ->byDateRange($startDate, $endDate)
            ->with(['subject', 'class'])
            ->orderBy('date')
            ->get();

        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'sick' => $attendances->where('status', 'sick')->count(),
            'permission' => $attendances->where('status', 'permission')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
        ];

        $stats['attendance_rate'] = $stats['total'] > 0
            ? round((($stats['present'] + $stats['late']) / $stats['total']) * 100, 2)
            : 0;

        return [
            'attendances' => $attendances,
            'stats' => $stats,
        ];
    }

    /**
     * Get students with low attendance
     */
    public function getStudentsWithLowAttendance($threshold = 75, $classId = null)
    {
        $query = Student::with(['class', 'attendances' => function ($q) {
            $q->thisMonth();
        }]);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        return $query->get()->filter(function ($student) use ($threshold) {
            $percentage = $student->getAttendancePercentage(
                now()->startOfMonth(),
                now()->endOfMonth()
            );

            return $percentage < $threshold;
        });
    }
}
