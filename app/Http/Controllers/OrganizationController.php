<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrganizationController extends Controller
{
    // Public listing
    public function index(Request $request)
    {
        $q    = trim((string)$request->get('q', ''));
        $sort = $request->get('sort', 'name');

        $table = 'organizations';
        $has   = fn($col) => Schema::hasColumn($table, $col);

        $builder = DB::table($table)->select("$table.*");

        if ($q !== '') {
            $builder->where(function ($w) use ($table, $q, $has) {
                $w->when($has('name'), fn($x) => $x->orWhere("$table.name", 'LIKE', "%{$q}%"))
                  ->when($has('description'), fn($x) => $x->orWhere("$table.description", 'LIKE', "%{$q}%"));
            });
        }

        if ($sort === 'newest' && $has('created_at')) {
            $builder->orderBy("$table.created_at", 'desc');
        } elseif ($has('name')) {
            $builder->orderBy("$table.name", 'asc');
        } else {
            $builder->orderBy("$table.id", 'desc');
        }

        $orgs = $builder->paginate(12)->appends($request->query());

        return view('organizations.index', compact('orgs', 'q', 'sort'));
    }
}
