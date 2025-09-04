<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;

class OpportunityPublicController extends Controller
{
    public function index(Request $request)
    {
        $allowedSorts = ['new','soon'];
        $sort = in_array($request->string('sort')->toString(), $allowedSorts, true) ? $request->string('sort')->toString() : 'soon';

        $builder = DB::table('opportunities');

        // Keyword
        if ($kw = trim((string)$request->query('q', ''))) {
            $builder->where(function($w) use ($kw) {
                $w->when(true, function($sub) use ($kw) {
                    $sub->where('title', 'like', '%'.$kw.'%');
                    if (Schema::hasColumn('opportunities','description')) {
                        $sub->orWhere('description', 'like', '%'.$kw.'%');
                    }
                });
            });
        }

        // Emirate / City
        if (Schema::hasColumn('opportunities','emirate')) {
            if ($em = trim((string)$request->query('emirate', ''))) {
                $builder->where('emirate', $em);
            }
        }
        if (Schema::hasColumn('opportunities','city')) {
            if ($city = trim((string)$request->query('city', ''))) {
                $builder->where('city', $city);
            }
        }

        // Category (if exists)
        if (Schema::hasColumn('opportunities','category_id')) {
            if ($cat = (int)$request->query('category_id', 0)) {
                $builder->where('category_id', $cat);
            }
        }

        // Status (if exists, prefer open/active)
        if (Schema::hasColumn('opportunities','status')) {
            if ($st = trim((string)$request->query('status', ''))) {
                $builder->where('status', $st);
            } else {
                $builder->whereNotIn('status', ['closed','archived']);
            }
        }

        // Sorting
        if ($sort === 'soon' && Schema::hasColumn('opportunities','start_at')) {
            $builder->orderBy('start_at', 'asc');
        } else {
            $builder->orderByDesc('created_at');
        }

        // Simple pagination
        $perPage = max(10, min(50, (int)$request->query('per_page', 12)));
        $opps = $builder->paginate($perPage)->appends($request->query());

        // Facets (for filters) — read distincts only when columns exist
        $emirates = Schema::hasColumn('opportunities','emirate')
            ? DB::table('opportunities')->select('emirate')->whereNotNull('emirate')->distinct()->pluck('emirate')
            : collect();
        $cities = Schema::hasColumn('opportunities','city')
            ? DB::table('opportunities')->select('city')->whereNotNull('city')->distinct()->pluck('city')
            : collect();
        $categories = Schema::hasTable('categories')
            ? DB::table('categories')->select('id','name')->orderBy('name')->get()
            : collect();

        return view('public.opportunities.index', compact('opps','emirates','cities','categories','sort'));
    }

    public function show(Request $request, int $opportunity)
    {
        $row = DB::table('opportunities')->where('id', $opportunity)->first();
        abort_unless($row, 404);

        // Build display/meta values defensively
        $title = $row->title ?? ('Opportunity #'.$row->id);
        $desc  = '';
        if (property_exists($row,'description') && $row->description) {
            $desc = mb_substr(strip_tags($row->description), 0, 160);
        }

        $canonical = url('/opportunities/'.$row->id.'-'.Str::slug($title));

        // JSON-LD (generic "Event" for broad compatibility)
        $ld = [
            '@context' => 'https://schema.org',
            '@type'    => 'Event',
            'name'     => $title,
            'description' => $desc ?: 'Volunteer opportunity',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'url'      => $canonical,
        ];
        if (property_exists($row,'start_at') && $row->start_at)  { $ld['startDate'] = (string)$row->start_at; }
        if (property_exists($row,'end_at') && $row->end_at)      { $ld['endDate']   = (string)$row->end_at; }
        if (property_exists($row,'city') && $row->city)          { $ld['location']['address']['addressLocality'] = $row->city; }
        if (property_exists($row,'emirate') && $row->emirate)    { $ld['location']['address']['addressRegion']   = $row->emirate; }

        return view('public.opportunities.show', [
            'row' => $row,
            'pageTitle' => $title,
            'description' => $desc,
            'canonical' => $canonical,
            'ld_json' => $ld,
        ]);
    }
}
