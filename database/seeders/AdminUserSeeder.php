<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@swaeduae.ae');
        $password = env('ADMIN_PASS', 'ChangeMe!Strong#2025');

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Super Admin', 'password' => Hash::make($password)]
        );

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Admin');
        }
    }
}
