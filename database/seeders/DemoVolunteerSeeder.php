<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoVolunteerSeeder extends Seeder
{
    public function run(): void
    {
        // Remove old demo volunteer if exists
        DB::table('users')->where('email', 'john@example.com')->delete();

        // Create demo volunteer
        $userId = DB::table('users')->insertGetId([
            'name'       => 'John Doe',
            'email'      => 'john@example.com',
            'password'   => Hash::make('password'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Link to volunteers table
        DB::table('volunteers')->insert([
            'user_id'    => $userId,
            'phone'      => '+971500000000',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
