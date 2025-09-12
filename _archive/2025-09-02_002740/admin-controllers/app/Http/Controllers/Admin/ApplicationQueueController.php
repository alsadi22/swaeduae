<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApplicationQueueController extends Controller
{
    public function index(Request $request)
    {
        $candidates = ['event_applications','event_registrations','event_volunteer','event_user'];
        $pivot = null;
        foreach ($candidates as $t) {
            if (Schema::hasTable($t)) { $pivot = $t; break; }
        }

        if (!$pivot) {
            $apps = collect([]);
            $missingPivot = true;
            return view('admin.applications.index', compact('apps','missingPivot'));
        }

        // Column discovery
        $statusCol   = Schema::hasColumn($pivot,'status')    ? 'status'    : (Schema::hasColumn($pivot,'state') ? 'state' : null);
        $appliedCol  = Schema::hasColumn($pivot,'applied_at')? 'applied_at': (Schema::hasColumn($pivot,'created_at') ? 'created_at' : null);
        $eventKey    = Schema::hasColumn($pivot,'event_id')  ? 'event_id'  : 'event_id'; // keep default
        $userKey     = Schema::hasColumn($pivot,'user_id')   ? 'user_id'   : 'user_id';  // keep default

        $selects = [
            'ev.id',
            'e.title as event_title',
            DB::raw('e.date as event_date'),
            'u.name as user_name',
            'u.email as user_email',
        ];
        $selects[] = $statusCol  ? DB::raw("ev.`$statusCol` as status") : DB::raw("'pending' as status");
        $selects[] = $appliedCol ? DB::raw("ev.`$appliedCol` as applied_at") : DB::raw("NULL as applied_at");

        $q = DB::table("$pivot as ev")
            ->join('events as e', "e.id", '=', "ev.$eventKey")
            ->join('users  as u', "u.id", '=', "ev.$userKey")
            ->select($selects);

        if ($statusCol) { $q->where("ev.$statusCol", 'pending'); }

        $apps = $q->orderByDesc('ev.id')->paginate(20);
        $missingPivot = false;

        return view('admin.applications.index', compact('apps','missingPivot'));
    }
}
