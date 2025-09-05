<?php
namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Opportunity;

class OppFiltersComposer
{
    public function compose(View $view): void
    {
        // Build once, reuse across both views; minimal columns, distinct+sort in DB.
        $cats = Opportunity::query()
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $regions = Opportunity::query()
            ->whereNotNull('region')
            ->select('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        $view->with(compact('cats','regions'));
    }
}
