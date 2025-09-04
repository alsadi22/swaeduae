<?php
namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SimilarOppsComposer
{
    public function compose(View $view)
    {
        // Use whatever context you have (category/region). Safe fallback: latest 3.
        $similar = DB::table('opportunities')->latest()->limit(3)->get(['id','title','region','created_at']);
        $view->with('similar', $similar);
    }
}
