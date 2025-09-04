<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RbacSeeder extends Seeder {
    public function run(): void {
        $perms = ['roles.manage','users.manage','kyc.review','opportunities.manage','settings.manage','audit.view','reports.view'];
        foreach ($perms as $p) Permission::findOrCreate($p, 'web');

        $admin      = Role::findOrCreate('admin', 'web');
        $orgManager = Role::findOrCreate('org_manager', 'web');
        $moderator  = Role::findOrCreate('moderator', 'web');

        $admin->syncPermissions($perms);
        $orgManager->syncPermissions(['opportunities.manage','kyc.review','reports.view']);
        $moderator->syncPermissions(['kyc.review']);

        if (class_exists(\App\Models\User::class)) {
            \App\Models\User::where('is_admin',1)->get()->each(function($u){
                if (method_exists($u,'assignRole')) $u->assignRole('admin');
            });
        }
    }
}
