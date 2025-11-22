<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Mata Pelajaran Umum
            ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam', 'credit_hours' => 2],
            ['code' => 'PKN', 'name' => 'Pendidikan Kewarganegaraan', 'credit_hours' => 2],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia', 'credit_hours' => 4],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'credit_hours' => 3],
            ['code' => 'MTK', 'name' => 'Matematika', 'credit_hours' => 4],
            ['code' => 'SEJ', 'name' => 'Sejarah', 'credit_hours' => 2],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani & Olahraga', 'credit_hours' => 2],

            // Mata Pelajaran Khusus Jurusan Kelistrikan
            ['code' => 'DKL', 'name' => 'Dasar Kelistrikan', 'credit_hours' => 4],
            ['code' => 'INL', 'name' => 'Instalasi Listrik', 'credit_hours' => 4],
            ['code' => 'STL', 'name' => 'Sistem Tenaga Listrik', 'credit_hours' => 4],
            ['code' => 'PKL', 'name' => 'Pemeliharaan Peralatan Listrik', 'credit_hours' => 3],
            ['code' => 'AKL', 'name' => 'Aplikasi Kelistrikan', 'credit_hours' => 2],

            // Mata Pelajaran Khusus Jurusan Otomotif
            ['code' => 'DOS', 'name' => 'Dasar Otomotif', 'credit_hours' => 4],
            ['code' => 'MKR', 'name' => 'Mekanik Kendaraan Ringan', 'credit_hours' => 4],
            ['code' => 'SOM', 'name' => 'Sistem Otomotif Modern', 'credit_hours' => 3],
            ['code' => 'PER', 'name' => 'Perawatan dan Perbaikan Mesin', 'credit_hours' => 3],
            ['code' => 'KBT', 'name' => 'Kelistrikan Body dan Transmisi', 'credit_hours' => 2],

            // Mata Pelajaran Khusus Jurusan Teknologi Informasi
            ['code' => 'PPL', 'name' => 'Pengembangan Perangkat Lunak', 'credit_hours' => 4],
            ['code' => 'JARKOM', 'name' => 'Jaringan Komputer', 'credit_hours' => 4],
            ['code' => 'BASDAT', 'name' => 'Basis Data', 'credit_hours' => 3],
            ['code' => 'KSBR', 'name' => 'Keamanan Siber', 'credit_hours' => 3],
            ['code' => 'TIK', 'name' => 'Teknologi Informasi dan Komunikasi', 'credit_hours' => 2],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
