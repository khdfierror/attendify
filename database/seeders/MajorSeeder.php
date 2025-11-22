<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $majors = [
            [
                'code' => 'KLS',
                'name' => 'Kelistrikan',
                'description' => 'Program studi Kelistrikan yang mempelajari dasar-dasar listrik, instalasi listrik, sistem tenaga listrik, pemeliharaan peralatan listrik, dan penerapan teknologi kelistrikan dalam kehidupan sehari-hari.',
                'is_active' => true,
            ],
            [
                'code' => 'OTO',
                'name' => 'Otomotif',
                'description' => 'Program studi Otomotif yang mempelajari tentang perakitan, perawatan, perbaikan, dan teknologi kendaraan bermotor, baik roda dua maupun roda empat, serta sistem dan komponen otomotif modern.',
                'is_active' => true,
            ],
            [
                'code' => 'IT',
                'name' => 'Teknologi Informasi',
                'description' => 'Program studi Teknologi Informasi yang mempelajari pengembangan perangkat lunak, jaringan komputer, basis data, keamanan siber, serta pemanfaatan teknologi komputer dan informasi dalam berbagai bidang.',
                'is_active' => true,
            ],
        ];

        foreach ($majors as $major) {
            Major::create($major);
        }
    }
}
