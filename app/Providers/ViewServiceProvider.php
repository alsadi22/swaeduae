<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // My Applications
        View::composer('account.applications', function ($view) {
            $user = Auth::user();
            if (!$user) { $view->with('apps', collect()); return; }

            $apps = DB::table('applications as a')
                ->join('opportunities as o', 'o.id', '=', 'a.opportunity_id')
                ->where('a.user_id', $user->id)
                ->orderByDesc('a.created_at')
                ->select([
                    'o.slug', 'o.title', 'o.location', 'o.starts_at', 'o.ends_at',
                    'a.status',
                ])->get()
                ->map(function ($r) {
                    return (object)[
                        'slug'      => $r->slug,
                        'title'     => $r->title ?: 'Untitled',
                        'location'  => $r->location,
                        'starts_at' => $r->starts_at ? Carbon::parse($r->starts_at) : null,
                        'ends_at'   => $r->ends_at ? Carbon::parse($r->ends_at) : null,
                        'status'    => $r->status ?: 'submitted',
                    ];
                });

            $view->with('apps', $apps);
        });

        // My Certificates (empty for now)
        View::composer('account.certificates', function ($view) {
            $view->with('certs', collect());
        });
    }
}
