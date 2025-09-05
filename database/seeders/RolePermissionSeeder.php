<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin','org','volunteer'];
        foreach ($roles as $r) { Role::findOrCreate($r, 'web'); }

        // Example permissions (extend as needed)
        $perms = [
            'manage users','manage organizations','manage opportunities',
            'issue certificates','view reports'
        ];
        foreach ($perms as $p) { Permission::findOrCreate($p, 'web'); }

        // Grant admin everything
        $admin = Role::findByName('admin', 'web');
        $admin->givePermissionTo(Permission::all());

        // Attach admin role to your admin user (by email)
        $u = User::where('email','whitewooolf@hotmail.com')->first();
        if ($u && !$u->hasRole('admin')) { $u->assignRole('admin'); }
    }
}
