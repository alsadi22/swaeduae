<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;

class DashboardCacheServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('org.partials.dashboard_v1', function ($view) {
            // If another composer already provided $kpi, respect it.
            $data = $view->getData();
            if (isset($data['kpi'])) return;

            $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
            $key = 'kpi:org:' . ($orgId ?: 0);

            $kpi = Cache::remember($key, 60, function () use ($orgId) {
                try {
                    $now = Carbon::now();
                    $out = ['upcoming'=>0,'appsTotal'=>0,'appsApproved'=>0,'appsPending'=>0,'checkinsToday'=>0];
                    if (!$orgId) return $out;

                    $oppIds = DB::table('opportunities')->where('organization_id', $orgId)->pluck('id');
                    $out['upcoming'] = DB::table('opportunities')->where('organization_id', $orgId)->where('start_at', '>=', $now)->count();
                    if ($oppIds->isEmpty()) return $out;

                    $out['appsTotal']    = DB::table('applications')->whereIn('opportunity_id', $oppIds)->count();
                    $out['appsApproved'] = DB::table('applications')->whereIn('opportunity_id', $oppIds)->where('status','approved')->count();
                    $out['appsPending']  = DB::table('applications')->whereIn('opportunity_id', $oppIds)->where('status','pending')->count();
                    $out['checkinsToday']= DB::table('attendances')->whereIn('opportunity_id', $oppIds)->whereDate('check_in_at', $now->toDateString())->count();
                    return $out;
                } catch (\Throwable $e) {
                    return ['upcoming'=>0,'appsTotal'=>0,'appsApproved'=>0,'appsPending'=>0,'checkinsToday'=>0];
                }
            });

            $view->with('kpi', $kpi);
        });
    }
}
