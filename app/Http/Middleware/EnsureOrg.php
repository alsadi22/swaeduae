<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnsureOrg
{
    public function handle(Request $request, Closure $next): Response
    {
        $u = $request->user();
        if (!$u) return redirect()->route('admin.login');

        // Admins may access org portal; try attaching their org for convenience
        if ((int)($u->is_admin ?? 0) === 1) {
            if ($org = $this->findOrgForUser($u->id)) {
                $request->attributes->set('org', $org);
            }
            return $next($request);
        }

        if (($u->role ?? 'user') !== 'org') {
            return redirect('/')->with('error', __('swaed.not_org_user') ?? 'Not authorized for organization portal.');
        }

        $org = $this->findOrgForUser($u->id);
        if (!$org && !$request->is('org/setup*')) {
            return redirect()->route('org.setup.form');
        }
        if ($org) $request->attributes->set('org', $org);

        return $next($request);
    }

    protected function findOrgForUser($uid)
    {
        if (!Schema::hasTable('organizations')) return null;

        $q = DB::table('organizations');
        $q->where(function($w) use ($uid) {
            if (Schema::hasColumn('organizations','owner_user_id')) $w->orWhere('owner_user_id', $uid);
            if (Schema::hasColumn('organizations','owner_id'))      $w->orWhere('owner_id', $uid);
            if (Schema::hasColumn('organizations','user_id'))       $w->orWhere('user_id', $uid);
        });

        return $q->first();
    }
}
