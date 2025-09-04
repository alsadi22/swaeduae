<?php
namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class OrgPartialsComposer
{
    public function compose(View $view)
    {
        $orgId = auth()->user()->organization_id ?? null;
        if (!$orgId) return;

        $now = Carbon::now();

        // Only compute what a given partial might need; safe defaults
        $data = [];

        if (Schema::hasTable('opportunities')) {
            $data['upcoming'] = DB::table('opportunities')
                ->where('organization_id',$orgId)
                ->where('start_at','>=',$now)->count();
        }

        if (Schema::hasTable('applications')) {
            $data['appsTotal'] = DB::table('applications')
                ->whereIn('opportunity_id', function($q) use($orgId){ $q->from('opportunities')->select('id')->where('organization_id',$orgId); })
                ->count();
            $data['appsApproved'] = DB::table('applications')
                ->where('status','approved')
                ->whereIn('opportunity_id', function($q) use($orgId){ $q->from('opportunities')->select('id')->where('organization_id',$orgId); })
                ->count();
            $data['appsPending']  = DB::table('applications')
                ->where('status','pending')
                ->whereIn('opportunity_id', function($q) use($orgId){ $q->from('opportunities')->select('id')->where('organization_id',$orgId); })
                ->count();
        }

        // Add more lightweight lookups here as needed by each partial…

        $view->with($data);
    }
}
