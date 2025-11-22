<?php

namespace Database\Seeders;

use App\Enums\Kelas;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = Classroom::all();

        foreach ($classes as $class) {
            for ($i = 1; $i <= 30; $i++) {
                // Pastikan $class->grade_level adalah int:
                $gradeLevel = is_object($class->grade_level) && $class->grade_level instanceof Kelas
                    ? $class->grade_level->value
                    : $class->grade_level;

                $nis = $gradeLevel.str_pad($class->id, 2, '0', STR_PAD_LEFT).str_pad($i, 3, '0', STR_PAD_LEFT);

                $user = User::create([
                    'name' => "Siswa {$class->name}-{$i}",
                    'email' => "siswa.{$nis}@school.com",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('siswa');

                Student::create([
                    'user_id' => $user->id,
                    'nis' => $nis,
                    'nisn' => '00'.rand(10000000, 99999999),
                    'full_name' => "Siswa {$class->name}-{$i}",
                    'nickname' => "Siswa $i",
                    'gender' => $i % 2 == 0 ? 'L' : 'P',
                    'birth_place' => 'Jakarta',
                    'birth_date' => now()->subYears(15 + $gradeLevel)->subMonths(rand(1, 12)),
                    'address' => "Jl. Pendidikan No. $i, Jakarta",
                    'phone' => '0812345'.str_pad($i, 4, '0', STR_PAD_LEFT),
                    'parent_name' => "Orang Tua Siswa $i",
                    'parent_phone' => '0856789'.str_pad($i, 4, '0', STR_PAD_LEFT),
                    'parent_email' => "ortu.{$nis}@email.com",
                    'class_id' => $class->id,
                    'major_id' => $class->major_id,
                    'enrollment_date' => now()->subYears($gradeLevel - 10),
                    'status' => 'active',
                ]);
            }
        }
    }
}
