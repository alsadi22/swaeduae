<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateAdminOrgUsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $orgEmail   = env('ORG_EMAIL');
        $adminPass  = env('ADMIN_PASSWORD', 'ChangeMe!123');
        $orgPass    = env('ORG_PASSWORD', 'ChangeMe!123');

        if ($adminEmail) {
            $u = User::firstOrCreate(['email'=>$adminEmail], [
                'name'=>'Admin', 'password'=>Hash::make($adminPass)
            ]);
            $u->assignRole('admin');
            $this->command->info("Admin: {$u->email}");
        } else {
            $this->command->warn('ADMIN_EMAIL not set in .env');
        }

        if ($orgEmail) {
            $o = User::firstOrCreate(['email'=>$orgEmail], [
                'name'=>'Org Manager', 'password'=>Hash::make($orgPass)
            ]);
            $o->assignRole('org');
            $this->command->info("Org: {$o->email}");
        } else {
            $this->command->warn('ORG_EMAIL not set in .env');
        }
    }
}
