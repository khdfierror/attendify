<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Guru $i",
                'email' => "guru$i@school.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('guru');

            Teacher::create([
                'user_id' => $user->id,
                'nip' => '198'.str_pad($i, 7, '0', STR_PAD_LEFT),
                'full_name' => "Guru $i",
                'gender' => $i % 2 == 0 ? 'L' : 'P',
                'phone' => '08123456789'.$i,
                'email' => "guru$i@school.com",
                'join_date' => now()->subYears(rand(1, 10)),
                'status' => 'active',
            ]);
        }
    }
}
