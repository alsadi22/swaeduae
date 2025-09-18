<?php
namespace App\Observers;
use App\Models\OrgProfile;
use App\Models\User;
use Spatie\Permission\Models\Role;

class OrgProfileObserver
{
    public function updated(OrgProfile $profile): void
    {
        if ($profile->wasChanged('status') && $profile->status === 'approved' && $profile->user_id) {
            $user = User::find($profile->user_id);
            if ($user && method_exists($user, 'assignRole') && ! $user->hasRole('org')) {
                try {
                    $guard = method_exists($user,'getDefaultGuardName') ? $user->getDefaultGuardName() : (config('auth.defaults.guard','web') ?: 'web');
                    Role::findOrCreate('org', $guard);
                    $user->assignRole('org');
                } catch (\Throwable $e) { /* ignore in tests */ }
            }
        }
    }
}
