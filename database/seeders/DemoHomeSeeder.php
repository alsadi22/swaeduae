<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoHomeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('news')->insert([
            'title'      => 'Sawaed UAE Launches New Platform',
            'content'    => 'We are excited to announce the launch of our new volunteer portal!', // Changed from 'body' to 'content'
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
