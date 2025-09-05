<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoOpportunitySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('opportunities')->insert([
            'title'       => 'Beach Cleanup Drive',
            'description' => 'Join us to clean up the beach and protect marine life.',
            'date'        => Carbon::now()->addDays(10)->toDateString(),
            'location'    => 'Dubai Marina',
            'status'      => 'active',
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);
    }
}
