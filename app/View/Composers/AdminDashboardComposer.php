<?php
namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminDashboardComposer
{
    public function compose(View $view)
    {
        $has = fn($t)=> Schema::hasTable($t);
        $hasC = fn($t,$c)=> Schema::hasColumn($t,$c);

        $users = class_exists(\App\Models\User::class) ? \App\Models\User::count() : ($has('users') ? DB::table('users')->count() : 0);
        $opps  = $has('opportunities') ? DB::table('opportunities')->count() : 0;
        $orgs  = $has('organizations') ? DB::table('organizations')->count() : 0;

        // hours total
        $hours = 0.0;
        if ($has('volunteer_hours')) {
            $hcol = $hasC('volunteer_hours','hours') ? 'hours' : ($hasC('volunteer_hours','duration') ? 'duration' : null);
            if ($hcol) $hours = (float) DB::table('volunteer_hours')->sum($hcol);
        }

        // Weekly stats
        $weeklyUsers = [];
        for ($i=0; $i<4; $i++) {
            $start = Carbon::now()->subWeeks($i+1);
            $end   = Carbon::now()->subWeeks($i);
            $weeklyUsers[] = $has('users') ? (int) DB::table('users')->whereBetween('created_at',[$start,$end])->count() : 0;
        }

        $recentUsers = $has('users')
            ? DB::table('users')->select('name','email','created_at')->orderByDesc('created_at')->limit(6)->get()
            : collect();

        $recentOpps  = $has('opportunities')
            ? DB::table('opportunities')->select('id','title','region','created_at')->orderByDesc('created_at')->limit(6)->get()
            : collect();

        $view->with(compact('users','opps','orgs','hours','weeklyUsers','recentUsers','recentOpps'));
    }
}
