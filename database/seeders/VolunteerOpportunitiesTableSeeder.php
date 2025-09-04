<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VolunteerOpportunitiesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('volunteer_opportunities')->insert([
            [
                'title' => 'Community Clean-up',
                'description' => 'Join us to clean the local park and make it greener.',
                'date' => Carbon::now()->addWeeks(1)->format('Y-m-d'),
                'location' => 'Dubai Park',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Food Drive Volunteer',
                'description' => 'Help collect and distribute food packages to families in need.',
                'date' => Carbon::now()->addWeeks(2)->format('Y-m-d'),
                'location' => 'Sharjah Community Center',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
