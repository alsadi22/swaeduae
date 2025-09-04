<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch existing users and opportunities
        $users = DB::table('users')->pluck('id')->toArray();
        $opps  = DB::table('opportunities')->pluck('id')->toArray();

        if (empty($users) || empty($opps)) {
            $this->command->warn('No users or opportunities found — skipping applications seed.');
            return;
        }

        // Insert demo applications
        $demoApplications = [];
        for ($i = 0; $i < 5; $i++) {
            $demoApplications[] = [
                'user_id' => $users[array_rand($users)],
                'opportunity_id' => $opps[array_rand($opps)],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('applications')->insertOrIgnore($demoApplications);
        $this->command->info('Demo applications seeded successfully!');
    }
}
