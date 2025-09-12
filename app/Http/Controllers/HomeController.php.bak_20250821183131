<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Skip fragile settings lookup; blade already has a default hero image.
        $cover = null;

        // Latest events
        $events = DB::table('events')
            ->select('id','title','city','location','date','start_time','end_time','region','category')
            ->orderByDesc('date')->orderByDesc('id')
            ->limit(20)->get()
            ->map(function ($e) {
                $e->type  = 'event';
                $e->slots = null;
                $e->mode  = stripos(($e->city.' '.$e->location), 'virtual') !== false ? 'virtual' : 'onsite';
                return $e;
            });

        // Latest opportunities (only open/published/NULL)
        $opps = DB::table('opportunities')
            ->select('id','title','city','location','date','start_time','end_time','region','category','slots','status')
            ->where(function ($w) {
                $w->where('status', 'open')
                  ->orWhere('status', 'published')
                  ->orWhereNull('status');
            })
            ->orderByDesc('date')->orderByDesc('id')
            ->limit(20)->get()
            ->map(function ($o) {
                $o->type = 'opportunity';
                $o->mode = stripos(($o->city.' '.$o->location), 'virtual') !== false ? 'virtual' : 'onsite';
                return $o;
            });

        // Merge, sort, and take only 6
        $tiles = $opps->merge($events)->sortByDesc('date')->take(6)->values();

        return view('home', compact('tiles','cover'));
    }
}
