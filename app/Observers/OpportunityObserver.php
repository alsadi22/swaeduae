<?php

namespace App\Observers;

use App\Models\Opportunity;
use Illuminate\Support\Facades\Cache;

class OpportunityObserver
{
    public function saved(Opportunity $opportunity): void
    {
        Cache::forget('nav.opps.latest');
        Cache::forget('nav.opps.count');
    }

    public function deleted(Opportunity $opportunity): void
    {
        Cache::forget('nav.opps.latest');
        Cache::forget('nav.opps.count');
    }
}
