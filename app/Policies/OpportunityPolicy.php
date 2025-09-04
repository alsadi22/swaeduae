<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Opportunity;
use Illuminate\Support\Facades\DB;

class OpportunityPolicy
{
    /**
     * Allow manage if:
     * - user is admin (has_role admin OR is_admin=1) OR
     * - user owns the organization that owns the opportunity
     */
    public function manage(User $user, Opportunity $opportunity): bool
    {
        // Admin bypass (works with either is_admin flag or Spatie role)
        if ((property_exists($user, 'is_admin') && $user->is_admin) ||
            (method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            return true;
        }

        // Resolve org ID owned by this user (owner_user_id linkage)
        $usersOrgId = DB::table('organizations')
            ->where('owner_user_id', $user->id)
            ->value('id');

        if (!$usersOrgId) {
            return false;
        }

        return (int)$opportunity->organization_id === (int)$usersOrgId;
    }
}
