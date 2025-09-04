<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Opportunity;
use Illuminate\Support\Str;

class DemoOpportunitiesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'title'      => 'Beach Cleanup - Jumeirah',
                'category'   => 'Environment',
                'city'       => 'Dubai',
                'location'   => 'Jumeirah Beach',
                'starts_at'  => now()->addDays(3),
                'ends_at'    => now()->addDays(3)->addHours(3),
            ],
            [
                'title'      => 'Blood Donation Drive',
                'category'   => 'Health',
                'city'       => 'Abu Dhabi',
                'location'   => 'Corniche',
                'starts_at'  => now()->addWeek(),
                'ends_at'    => now()->addWeek()->addHours(4),
            ],
            [
                'title'      => 'Food Bank Packing',
                'category'   => 'Community',
                'city'       => 'Sharjah',
                'location'   => 'Industrial Area 3',
                'starts_at'  => now()->addDays(10),
                'ends_at'    => now()->addDays(10)->addHours(2),
            ],
            [
                'title'      => 'Park Tree Planting',
                'category'   => 'Environment',
                'city'       => 'Ajman',
                'location'   => 'Al Jurf',
                'starts_at'  => now()->addDays(14),
                'ends_at'    => now()->addDays(14)->addHours(3),
            ],
        ];

        foreach ($rows as $r) {
            Opportunity::firstOrCreate(
                ['title' => $r['title']],
                array_merge($r, [
                    'description'    => 'Join us to make an impact. Tasks include setup, registration, and coordination.',
                    'organizer_id'   => null, // set to 1 if you have an org/admin user you want as owner
                    'checkin_token'  => Str::random(32),
                    'checkout_token' => Str::random(32),
                ])
            );
        }
    }
}
