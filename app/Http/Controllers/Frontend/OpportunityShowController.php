<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OpportunityShowController extends Controller
{
    public function __invoke($id)
    {
        $opportunity = DB::table('opportunities')
            ->where('id', is_numeric($id) ? (int)$id : -1)
            ->first();

        if (!$opportunity) {
            abort(404);
        }

        // Provide graceful defaults some older blades might expect
        $starts_on = $opportunity->starts_on ?? $opportunity->start_date ?? null;
        $ends_on   = $opportunity->ends_on   ?? $opportunity->end_date   ?? null;

        return view('opportunities.public_show', compact('opportunity','starts_on','ends_on'));
    }
}
