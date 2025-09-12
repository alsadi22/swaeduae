<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoContentSeeder extends Seeder
{
    // --- tiny helpers (no Faker needed) ---
    private function pick(array $a) { return $a[array_rand($a)]; }
    private function words(int $n): string {
        $pool = ['Community','Health','Environment','Education','Youth','Family','Digital','Green','Innovation','Support','Care','Culture','Future','Hope','Unity','Service','Action','Bridge','Network','Hub','Drive','Festival','Project','Cleanup','Campaign','Workshop','Summit','Forum','Day'];
        shuffle($pool);
        return strtolower(implode(' ', array_slice($pool, 0, $n)));
    }
    private function title(int $n): string { return Str::title($this->words($n)); }
    private function sentence(int $n = 6): string { return Str::title($this->words($n)).'.'; }
    private function paragraph(int $sentences = 2): string {
        $out = [];
        for ($i=0; $i<$sentences; $i++) $out[] = $this->sentence(rand(5,9));
        return implode(' ', $out);
    }

    public function run(): void
    {
        // Uncomment if you want a clean slate:
        // DB::table('events')->truncate();
        // DB::table('opportunities')->truncate();
        // DB::table('organizations')->truncate();

        $cities  = ['Abu Dhabi','Dubai','Sharjah','Ajman','Fujairah','Ras Al Khaimah','Umm Al Quwain'];
        $regions = ['Abu Dhabi','Dubai','Sharjah','Ajman','Fujairah','RAK','UAQ'];
        $oppCats = ['Community','Health','Environment','Education','Work'];
        $evCats  = ['Workshop','Cleanup','Fundraiser','Training','Family'];

        // Organizations
        $orgIds = [];
        for ($i=0; $i<3; $i++) {
            $name  = $this->title(2).' Organization';
            $email = Str::slug($name).rand(100,999).'@example.com';
            $orgIds[] = DB::table('organizations')->insertGetId([
                'owner_user_id' => null,
                'name'         => $name,
                'email'        => $email,
                'password'     => bcrypt('password'),
                'address'      => rand(100,999).' '.Str::title($this->words(2)).' St',
                'kyc_document' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // Events (10)
        for ($i=0; $i<10; $i++) {
            $date   = Carbon::now()->addDays(rand(-5, 30));
            $minute = [0,30][rand(0,1)];
            $start  = Carbon::createFromTime(rand(8,18), $minute);
            $end    = (clone $start)->addHours(rand(1,3));

            DB::table('events')->insert([
                'organization_id'      => $orgIds[array_rand($orgIds)],
                'title'                => $this->title(rand(2,4)),
                'city'                 => $cities[array_rand($cities)],
                'description'          => $this->paragraph(3),
                'summary'              => $this->sentence(7),
                'region'               => $regions[array_rand($regions)],
                'category'             => $evCats[array_rand($evCats)],
                'date'                 => $date->format('Y-m-d'),
                'start_time'           => $start->format('H:i'),
                'end_time'             => $end->format('H:i'),
                'capacity'             => rand(20, 120),
                'status'               => 'published',
                'location'             => rand(100,999).' '.Str::title($this->words(2)).' Hall',
                'application_deadline' => $date->copy()->subDays(rand(1,5))->format('Y-m-d'),
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }

        // Opportunities (16)
        for ($i=0; $i<16; $i++) {
            $date   = Carbon::now()->addDays(rand(0, 35));
            $minute = [0,30][rand(0,1)];
            $start  = Carbon::createFromTime(rand(8,18), $minute);
            $end    = (clone $start)->addHours(rand(1,4));
            $city   = $cities[array_rand($cities)];
            $virtual = (bool) rand(0,3) === 0; // ~25% virtual

            DB::table('opportunities')->insert([
                'organizer_id'   => $orgIds[array_rand($orgIds)],
                'owner_id'       => null,
                'title'          => $this->title(3),
                'summary'        => $this->sentence(8),
                'description'    => $this->paragraph(3),
                'category'       => $oppCats[array_rand($oppCats)],
                'category_id'    => null,
                'city'           => $virtual ? 'Virtual' : $city,
                'location'       => $virtual ? 'Virtual' : rand(10,99).' '.Str::title($this->words(2)).' Center',
                'date'           => $date->format('Y-m-d'),
                'start_time'     => $start->format('H:i'),
                'end_time'       => $end->format('H:i'),
                'slots'          => rand(8,60),
                'status'         => 'open',
                'region'         => $regions[array_rand($regions)],
                'badge'          => rand(0,4) === 0 ? 'Featured' : null,
                'featured'       => rand(0,2) === 0,
                'checkin_token'  => Str::random(8),
                'checkout_token' => Str::random(8),
                'is_completed'   => 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
