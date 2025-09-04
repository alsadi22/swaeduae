<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (empty($roles)) { $roles = ['org']; } // default if none passed

        $flat = [];
        foreach ($roles as $r) {
            foreach (preg_split('/[|,]/', (string)$r, -1, PREG_SPLIT_NO_EMPTY) as $p) {
                $flat[] = strtolower(trim($p));
            }
        }
        $roles = array_values(array_unique($flat));

        $user = Auth::user();
        if (!$user) {
            return redirect()->guest(route('admin.login'));
        }

        $roleText = strtolower((string)($user->role ?? ''));
        $isAdmin = (int)($user->is_admin ?? 0) === 1
            || in_array($roleText, ['admin','superadmin'], true);

        if (in_array('admin', $roles, true)) {
            abort_if(!$isAdmin, 403);
            return $next($request);
        }

        if (in_array('org', $roles, true)) {
            $isOrg = $isAdmin
                || in_array($roleText, ['org','organization','org_owner','organization_admin','org_manager'], true)
                || DB::table('organizations')->where('owner_user_id', $user->id)->exists();

            abort_if(!$isOrg, 403);
            return $next($request);
        }

        return $next($request);
    }
}
