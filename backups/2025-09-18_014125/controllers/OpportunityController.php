<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OpportunityController extends Controller
{
    public function index(Request $request)
    {
        $q         = trim((string)$request->get('q', ''));
        $category  = $request->get('category');
        $region    = $request->get('region');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');
        $sort      = $request->get('sort', 'newest');

        $table = 'opportunities';
        $has   = fn($col) => Schema::hasColumn($table, $col);

        $builder = DB::table($table);

        // Basic select
        $builder->select("$table.*");

        // Searching
        if ($q !== '') {
            $builder->where(function ($w) use ($q, $table, $has) {
                $w->when($has('title'), fn($x) => $x->orWhere("$table.title", 'LIKE', "%{$q}%"))
                  ->when($has('description'), fn($x) => $x->orWhere("$table.description", 'LIKE', "%{$q}%"));
            });
        }

        // Filters
        if (!empty($category) && $has('category')) {
            $builder->where("$table.category", $category);
        }
        if (!empty($region) && $has('region')) {
            $builder->where("$table.region", $region);
        }
        if (!empty($dateFrom) && $has('start_date')) {
            $builder->whereDate("$table.start_date", '>=', $dateFrom);
        }
        if (!empty($dateTo) && $has('end_date')) {
            $builder->whereDate("$table.end_date", '<=', $dateTo);
        }

        // Sorting
        $closingExpr = DB::raw("COALESCE($table.deadline, $table.end_date, $table.start_date, $table.created_at)");
        if ($sort === 'closing_soon') {
            $builder->orderBy($closingExpr, 'asc');
        } else {
            $builder->orderBy("$table.created_at", 'desc');
        }

        $opportunities = $builder->paginate(12)->appends($request->query());
        return view('opportunities.index', compact('opportunities', 'q', 'category', 'region', 'dateFrom', 'dateTo', 'sort'));
    }

    public function show($idOrSlug)
    {
        $table = 'opportunities';
        $has = fn($col) => Schema::hasColumn($table, $col);

        $query = DB::table($table)->select("$table.*");
        if ($has('slug') && !is_numeric($idOrSlug)) {
            $query->where("$table.slug", $idOrSlug);
        } else {
            $query->where("$table.id", (int)$idOrSlug);
        }

        $opportunity = $query->first();
        abort_if(!$opportunity, 404);

        return view('opportunities.show', compact('opportunity'));
    }
}
