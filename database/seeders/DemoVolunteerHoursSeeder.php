<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoVolunteerHoursSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('volunteer_hours')->insert([
            'user_id'     => 1,
            'event_id'    => 1,
            'hours'       => 5,
            'date'        => Carbon::now()->toDateString(),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
    }
}
