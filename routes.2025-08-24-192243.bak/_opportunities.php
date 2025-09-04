<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

Route::get('/opportunities', function (Request $r) {
    $q = trim($r->get('q', ''));

    if (!Schema::hasTable('opportunities')) {
        $rows = collect([]); $cats = collect([]); $regions = collect([]);
        $view = collect(['opportunities.public.index','opportunities.index','public.opportunities'])
            ->first(fn($v) => view()->exists($v)) ?? 'opportunities.public.index';
        return view($view, compact('rows','cats','regions','q') + ['opportunities'=>$rows]);
    }

    $cols = Schema::getColumnListing('opportunities');

    $title  = in_array('title',$cols) ? 'title' : (in_array('name',$cols) ? 'name' : null);
    $desc   = in_array('description',$cols) ? 'description' : (in_array('details',$cols) ? 'details' : null);
    $region = in_array('region',$cols) ? 'region' : (in_array('emirate',$cols) ? 'emirate' : null);
    $city   = in_array('city',$cols) ? 'city' : null;
    $cat    = in_array('category',$cols) ? 'category' : null;
    $date   = collect(['start_date','date','event_date','starts_at'])->first(fn($c)=>in_array($c,$cols));

    $query = DB::table('opportunities');
    $select = array_values(array_filter(['id',$title,$city,$region,$cat,$date,$desc]));
    if ($select) $query->select($select);

    if ($q && ($title || $desc)) {
        $query->where(function($w) use ($q,$title,$desc){
            if ($title) $w->where($title,'like',"%$q%");
            if ($desc)  $w->orWhere($desc,'like',"%$q%");
        });
    }
    if ($cat && $r->filled('category'))  $query->where($cat, $r->category);
    if ($region && $r->filled('region')) $query->where($region, $r->region);

    if ($date) $query->orderByDesc($date);
    $query->orderByDesc('id');

    $rows = $query->paginate(24);
    $rows->appends($r->query());

    $cats    = $cat    ? DB::table('opportunities')->whereNotNull($cat)->distinct()->orderBy($cat)->pluck($cat) : collect();
    $regions = $region ? DB::table('opportunities')->whereNotNull($region)->distinct()->orderBy($region)->pluck($region) : collect();

    $view = collect(['opportunities.public.index','opportunities.index','public.opportunities'])
            ->first(fn($v) => view()->exists($v)) ?? 'opportunities.public.index';

    return view($view, [
        'rows'=>$rows, 'opportunities'=>$rows,
        'cats'=>$cats, 'regions'=>$regions, 'q'=>$q,
    ]);
})->name('opportunities.index');

Route::get('/opportunities/{id}', function ($id) {
    if (!Schema::hasTable('opportunities')) abort(404);
    $o = DB::table('opportunities')->where('id',$id)->first();
    abort_unless($o, 404);

    $view = collect(['opportunities.public.show','opportunities.show'])
            ->first(fn($v)=>view()->exists($v)) ?? 'opportunities.public.show';

    return view($view, ['o'=>$o, 'opportunity'=>$o]);
})->whereNumber('id')->name('opps.public.show');
