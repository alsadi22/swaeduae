<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OpportunityCapController extends Controller
{
    public function update(Request $request, Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);

        $data = $request->validate([
            'slot_cap' => ['nullable','integer','min:0','max:100000'],
        ]);

        $cap = $data['slot_cap'] ?? null;

        if (Schema::hasColumn('opportunities', 'slot_cap')) {
            DB::table('opportunities')->where('id', $opportunity->id)->update([
                'slot_cap'  => $cap,
                'updated_at'=> now(),
            ]);
        } else {
            // Fallback to settings table
            DB::table('settings')->updateOrInsert(
                ['key' => "opp:{$opportunity->id}:slot_cap"],
                ['value' => $cap, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // (Optional) audit
        DB::table('audit_logs')->insert([
            'actor_id'    => auth()->id(),
            'action'      => 'opportunity.slot_cap.updated',
            'entity_type' => 'opportunity',
            'entity_id'   => $opportunity->id,
            'note'        => json_encode(['slot_cap' => $cap]),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('status', __('Slot cap saved'));
    }
}
