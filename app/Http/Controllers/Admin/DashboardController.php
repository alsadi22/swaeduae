<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'         => (int) DB::table('users')->count(),
            'organizations' => (int) DB::table('org_profiles')->count(),
            'opportunities' => (int) DB::table('opportunities')->count(),
            'certificates'  => (int) DB::table('certificates')->count(),
        ];

        // Try your existing dashboard views first, then fallback.
        $candidates = [
            'admin.dashboard.index',
            'admin.dashboard',         // your prior blade
            'admin.home',
            'admin.index',
            'admin/panel/dashboard',
        ];

        foreach ($candidates as $view) {
            if (view()->exists($view)) {
                return view($view, compact('stats'));
            }
        }

        // Fallback (kept only as a last resort)
        return view('admin.dashboard', compact('stats'));
    }
}
