<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Partner;
use App\Models\Event;
use App\Models\Opportunity;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        Partner::factory()->count(5)->create();
        Event::factory()->count(10)->create();
        Opportunity::factory()->count(10)->create();
    }
}
