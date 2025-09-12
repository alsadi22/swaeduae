<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $q = DB::table('opportunities');

        // Prefer upcoming/featured if columns exist
        if (Schema::hasColumn('opportunities', 'featured')) {
            $q->orderByDesc('featured');
        }
        if (Schema::hasColumn('opportunities', 'start_at')) {
            $q->orderBy('start_at');
        } else {
            $q->orderByDesc('created_at');
        }

        $opps = $q->limit(8)->get();

        return view('public.home', [
            'opps' => $opps,
        ]);
    }
}
