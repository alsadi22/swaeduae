<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $certs = $hours = $apps = collect();
        $totMinutes = 0;

        if (Schema::hasTable('certificates')) {
            $certs = DB::table('certificates')->where('user_id',$u->id)->orderByDesc('id')->limit(5)->get();
        }
        if (Schema::hasTable('volunteer_hours')) {
            $hours = DB::table('volunteer_hours')->where('user_id',$u->id)->orderByDesc('updated_at')->limit(5)->get();
            foreach ($hours as $h) { $totMinutes += (int)($h->minutes ?? 0); }
        }
        if (Schema::hasTable('applications')) {
            $apps = DB::table('applications')->where('user_id',$u->id)->orderByDesc('id')->limit(5)->get();
        }

        return view('my/dashboard', [
            'certs'=>$certs, 'hours'=>$hours, 'apps'=>$apps,
            'totMinutes'=>$totMinutes
        ]);
    }
}
