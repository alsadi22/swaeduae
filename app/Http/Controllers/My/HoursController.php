<?php

namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HoursController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        if (!Schema::hasTable('volunteer_hours')) { return view('my/hours', ['rows'=>collect(),'total'=>0]); }
        $rows = DB::table('volunteer_hours as h')
            ->leftJoin('opportunities as o','o.id','=','h.opportunity_id')
            ->select('h.*','o.title as opportunity_title')
            ->where('h.user_id',$u->id)->orderByDesc('h.updated_at')->paginate(20);
        $total = DB::table('volunteer_hours')->where('user_id',$u->id)->sum('minutes');
        return view('my/hours', compact('rows','total'));
    }
}
