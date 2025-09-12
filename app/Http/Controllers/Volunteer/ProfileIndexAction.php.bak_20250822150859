<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileIndexAction extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->guest('/login');
        }

        // --- Stats (safe even if some tables don't exist) ---
        $has = fn(string $t) => Schema::hasTable($t);

        $certCount = $has('certificates')
            ? (int) DB::table('certificates')->where('user_id', $user->id)->count()
            : 0;

        $totalHours = 0;
        if ($has('volunteer_hours')) {
            $totalHours = (int) DB::table('volunteer_hours')->where('user_id', $user->id)->sum('hours');
        } elseif ($has('certificates')) {
            $totalHours = (int) DB::table('certificates')->where('user_id', $user->id)->sum('hours');
        }

        $upcoming = collect();
        $upcomingCount = 0;
        if ($has('opportunity_applications') && $has('opportunities')) {
            $upcoming = DB::table('opportunity_applications')
                ->join('opportunities', 'opportunity_applications.opportunity_id', '=', 'opportunities.id')
                ->where('opportunity_applications.user_id', $user->id)
                ->whereDate('opportunities.start_date', '>=', now()->toDateString())
                ->orderBy('opportunities.start_date')
                ->select('opportunities.id', 'opportunities.title', 'opportunities.city', 'opportunities.region', 'opportunities.start_date')
                ->limit(6)
                ->get();
            $upcomingCount = $upcoming->count();
        }

        $latestCerts = $has('certificates')
            ? DB::table('certificates')
                ->where('user_id', $user->id)
                ->orderByDesc('issued_at')->orderByDesc('id')
                ->limit(5)->get()
            : collect();

        // Prefer the richer, themed dashboard
        if (View::exists('volunteer.profile')) {
            return view('volunteer.profile', compact('user', 'certCount', 'totalHours', 'upcomingCount', 'upcoming', 'latestCerts'));
        }

        // Legacy fallbacks (kept)
        foreach (['profile.edit', 'my.profile', 'volunteer.profile_edit', 'profile.show'] as $v) {
            if (View::exists($v)) {
                return view($v, compact('user'));
            }
        }

        return response()->view('errors.inline-profile-fallback', compact('user'), 200);
    }
}
