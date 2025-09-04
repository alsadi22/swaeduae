<?php
namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrgShortlistComposer
{
    public function compose(View $view)
    {
        $orgId = auth()->user()->organization_id ?? null;
        if (!$orgId) return;

        $data = [];
        if (Schema::hasTable('opportunities')) {
            // Provide $opps list for filters, etc.
            $data['opps'] = DB::table('opportunities')
                ->where('organization_id',$orgId)
                ->orderByDesc('created_at')->get(['id','title']);
        }
        // Slot cap / counters can be added similarly if the partial expects them.
        $view->with($data);
    }
}
