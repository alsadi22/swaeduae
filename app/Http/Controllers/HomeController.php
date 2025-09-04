<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        // Latest events (guard table existence)
        $events = Schema::hasTable('events')
            ? DB::table('events')
                ->select('id','title','city','location','date','start_time','end_time','region','category')
                ->orderByDesc('date')->orderByDesc('id')
                ->limit(20)->get()
                ->map(function ($e) {
                    $e->type  = 'event';
                    $e->slots = null;
                    $e->mode  = stripos(($e->city.' '.$e->location), 'virtual') !== false ? 'virtual' : 'onsite';
                    return $e;
                })
            : collect();

        // Latest opportunities (guard table existence)
        $opps = Schema::hasTable('opportunities')
            ? DB::table('opportunities')
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
                })
            : collect();

        $tiles = collect($events)->merge($opps);

        return view('home', [
            'cover' => null,
            'tiles' => $tiles,
            'opps'  => $opps,
        ]);
    }
}
