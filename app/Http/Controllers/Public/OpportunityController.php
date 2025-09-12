<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Support\Carbon;

class OpportunityController extends Controller
{
    public function index()
    {
        $items = Opportunity::orderBy('starts_at')->paginate(12)->through(function($o){
            return (object)[
                'title'     => $o->title ?? 'Untitled',
                'slug'      => $o->slug,
                'location'  => $o->location,
                'starts_at' => $o->starts_at ? Carbon::parse($o->starts_at) : null,
                'ends_at'   => $o->ends_at ? Carbon::parse($o->ends_at) : null,
            ];
        });
        return view('public.opportunities.index', ['items' => $items]);
    }

    public function show(string $slug)
    {
        $o = Opportunity::where('slug',$slug)->firstOrFail();
        return view('public.opportunities.show', ['op' => $o]);
    }
}
