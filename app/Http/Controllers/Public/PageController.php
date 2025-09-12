<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    public function about()   { return view('pages.about'); }
    public function faq()     { return view('pages.faq'); }
    public function partners(){ return view('pages.partners'); }

    public function regionsIndex()
    {
        $regions = [
            ['slug'=>'abu-dhabi','name'=>'Abu Dhabi'],
            ['slug'=>'dubai','name'=>'Dubai'],
            ['slug'=>'sharjah','name'=>'Sharjah'],
            ['slug'=>'ajman','name'=>'Ajman'],
            ['slug'=>'uaq','name'=>'UAQ'],
            ['slug'=>'rak','name'=>'RAK'],
            ['slug'=>'fujairah','name'=>'Fujairah'],
        ];
        return view('pages.regions.index', compact('regions'));
    }

    public function regionShow(string $slug)
    {
        $map = [
            'abu-dhabi'=>'Abu Dhabi','dubai'=>'Dubai','sharjah'=>'Sharjah','ajman'=>'Ajman',
            'uaq'=>'UAQ','rak'=>'RAK','fujairah'=>'Fujairah',
        ];
        $region = $map[$slug] ?? null;
        abort_unless($region, 404);

        $opportunities = collect();
        if (Schema::hasTable('opportunities') && Schema::hasColumn('opportunities','region')) {
            $opportunities = DB::table('opportunities')
                ->where('region', $region)->orderByDesc('id')->limit(50)->get();
        }
        return view('pages.regions.show', compact('region','opportunities'));
    }
}
