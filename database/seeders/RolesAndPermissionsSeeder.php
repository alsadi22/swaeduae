<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Volunteer','Organization','Admin','Auditor'];
        foreach ($roles as $r) { Role::findOrCreate($r); }

        $perms = [
            'opportunity.create','opportunity.update','opportunity.delete',
            'application.review','hours.verify','certificate.issue','admin.access'
        ];
        foreach ($perms as $p) { Permission::findOrCreate($p); }

        // tie a few sensible defaults
        Role::findByName('Organization')->givePermissionTo(['opportunity.create','opportunity.update','application.review','hours.verify']);
        Role::findByName('Admin')->givePermissionTo($perms);
    }
}
