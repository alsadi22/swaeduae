<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Opportunity;

class OpportunityBrowseController extends Controller
{
    public function index(Request $r)
    {
        $q        = trim((string)$r->input('q',''));
        $category = trim((string)$r->input('category',''));
        $city     = trim((string)$r->input('city',''));
        $from     = $r->input('from');
        $to       = $r->input('to');

        $query = Opportunity::query();

        if ($q !== '') {
            $query->where(function($w) use ($q){
                $w->where('title', 'like', "%$q%")
                  ->orWhere('description', 'like', "%$q%");
            });
        }
        if ($category !== '') $query->where('category', $category);
        if ($city !== '')     $query->where('city', $city);
        if ($from)            $query->whereDate('starts_at', '>=', $from);
        if ($to)              $query->whereDate('ends_at',   '<=', $to);

        $list = $query->latest('starts_at')->paginate(12)->withQueryString();

        // facet lists (distinct categories/cities for filters)
        $categories = Opportunity::whereNotNull('category')->where('category','<>','')->distinct()->pluck('category')->sort()->values();
        $cities     = Opportunity::whereNotNull('city')->where('city','<>','')->distinct()->pluck('city')->sort()->values();

        return view('opportunities.index', compact('list','q','category','city','from','to','categories','cities'));
    }
}
