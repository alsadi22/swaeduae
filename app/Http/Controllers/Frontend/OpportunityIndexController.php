<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpportunityIndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string)$request->query('q', ''));
        $query = DB::table('opportunities');

        if ($q !== '') {
            $query->where(function($w) use ($q) {
                $w->where('title', 'like', "%$q%");
                if (DB::getSchemaBuilder()->hasColumn('opportunities', 'description')) {
                    $w->orWhere('description', 'like', "%$q%");
                }
                if (DB::getSchemaBuilder()->hasColumn('opportunities', 'location')) {
                    $w->orWhere('location', 'like', "%$q%");
                }
            });
        }

        $opportunities = $query->orderByDesc('id')->paginate(12)->withQueryString();

        return view('opportunities.index_public', [
            'opportunities' => $opportunities,
            'q' => $q,
        ]);
    }
}
