<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Support\OrgMetrics;

class OrgDashboardServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $request = request();
            if (!$request || !$request->route()) {
                return;
            }

            $routeName = optional($request->route())->getName();
            $onOrgDash = ($routeName && strpos($routeName, 'org.dashboard') === 0)
                        || $request->is('org') || $request->is('org/dashboard');

            if (!$onOrgDash) {
                return;
            }

            $user = Auth::user();
            if (!$user || !isset($user->organization_id) || empty($user->organization_id)) {
                return;
            }

            $range = $request->query('range');
            $fromQ = $request->query('from');
            $toQ   = $request->query('to');

            if ($fromQ && $toQ) {
                try {
                    $from = Carbon::parse($fromQ)->startOfDay();
                    $to   = Carbon::parse($toQ)->endOfDay();
                } catch (\Throwable $e) {
                    $from = Carbon::now()->subDays(29)->startOfDay();
                    $to   = Carbon::now()->endOfDay();
                }
            } elseif ($range === '7d') {
                $from = Carbon::now()->subDays(6)->startOfDay();
                $to   = Carbon::now()->endOfDay();
            } else {
                $from = Carbon::now()->subDays(29)->startOfDay(); // default 30d window
                $to   = Carbon::now()->endOfDay();
            }

            $m = OrgMetrics::compute((int)$user->organization_id, $from, $to);
            $presentRate = $m['attendance_total'] > 0
                ? round(($m['present'] / max(1, $m['attendance_total'])) * 100)
                : 0;

            $view->with('orgDashFrom', $from->toDateString());
            $view->with('orgDashTo', $to->toDateString());
            $view->with('orgDashMetrics', [
                'total_minutes'    => $m['total_minutes'],
                'attendance_total' => $m['attendance_total'],
                'present'          => $m['present'],
                'no_show'          => $m['no_show'],
                'present_rate'     => $presentRate,
                'unfinalized'      => $m['unfinalized'],
            ]);
        });
    }
}
