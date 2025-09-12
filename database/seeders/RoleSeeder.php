<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::updateOrCreate(['name' => 'volunteer'], ['description' => 'Regular volunteer']);
        Role::updateOrCreate(['name' => 'organization'], ['description' => 'Organization user']);
        Role::updateOrCreate(['name' => 'admin'], ['description' => 'System administrator']);
        Role::updateOrCreate(['name' => 'sub-admin'], ['description' => 'Limited admin']);
    }
}
