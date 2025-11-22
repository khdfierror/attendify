<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::create([
            'name' => '2025/2026',
            'start_year' => 2025,
            'end_year' => 2026,
            'start_date' => '2025-07-15',
            'end_date' => '2026-06-30',
            'is_active' => true,
            'description' => 'Tahun Ajaran 2025/2026',
        ]);

        AcademicYear::create([
            'name' => '2024/2025',
            'start_year' => 2024,
            'end_year' => 2025,
            'start_date' => '2024-07-15',
            'end_date' => '2025-06-30',
            'is_active' => false,
            'description' => 'Tahun Ajaran 2024/2025',
        ]);
    }
}
