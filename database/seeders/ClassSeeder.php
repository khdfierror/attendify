<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassRoom;
use App\Models\Major;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $majors = Major::all();
        $teachers = User::role('guru')->get();

        $classes = [
            // Kelas 10
            ['name' => 'VII-A', 'grade_level' => 7, 'major_id' => null],
            ['name' => 'VII-B', 'grade_level' => 7, 'major_id' => null],
            ['name' => 'VII-C', 'grade_level' => 7, 'major_id' => null],

            // Kelas 11 KLS
            ['name' => 'VIII-KLS-1', 'grade_level' => 8, 'major_id' => $majors->where('code', 'KLS')->first()->id],
            ['name' => 'VIII-KLS-2', 'grade_level' => 8, 'major_id' => $majors->where('code', 'KLS')->first()->id],

            // Kelas 8 OTO
            ['name' => 'VIII-OTO-1', 'grade_level' => 8, 'major_id' => $majors->where('code', 'OTO')->first()->id],
            ['name' => 'VIII-OTO-2', 'grade_level' => 8, 'major_id' => $majors->where('code', 'OTO')->first()->id],

            // Kelas 8 IT
            ['name' => 'VIII-IT-1', 'grade_level' => 8, 'major_id' => $majors->where('code', 'IT')->first()->id],
            ['name' => 'VIII-IT-2', 'grade_level' => 8, 'major_id' => $majors->where('code', 'IT')->first()->id],

            // Kelas 12 KLS
            ['name' => 'IX-KLS-1', 'grade_level' => 9, 'major_id' => $majors->where('code', 'KLS')->first()->id],

            // Kelas 9 OTO
            ['name' => 'IX-OTO-1', 'grade_level' => 9, 'major_id' => $majors->where('code', 'OTO')->first()->id],

            // Kelas 9 IT
            ['name' => 'IX-IT-1', 'grade_level' => 9, 'major_id' => $majors->where('code', 'IT')->first()->id],
        ];

        foreach ($classes as $index => $classData) {
            Classroom::create([
                'name' => $classData['name'],
                'grade_level' => $classData['grade_level'],
                'major_id' => $classData['major_id'],
                'academic_year_id' => $academicYear->id,
                'homeroom_teacher_id' => $teachers[$index % $teachers->count()]->id,
                'max_students' => 40,
                'is_active' => true,
            ]);
        }
    }
}
