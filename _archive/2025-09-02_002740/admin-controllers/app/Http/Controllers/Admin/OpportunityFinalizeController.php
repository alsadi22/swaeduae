<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OpportunityFinalizeController extends Controller
{
    // Form page to finalize by Opportunity ID
    public function form()
    {
        return view('admin.opportunities.finalize_form');
    }

    // POST form handler -> delegates to ID-based method
    public function finalizeByForm(Request $request, CertificateService $svc)
    {
        $id = (int) $request->input('opportunity_id', 0);
        if ($id <= 0) {
            return back()->with('error', 'Please enter a valid opportunity ID.');
        }
        return $this->finalizeCertificates($request, $id, $svc);
    }

    // Original: finalize all attendees for a given opportunity ID
    public function finalizeCertificates(Request $request, int $id, CertificateService $svc)
    {
        // Collect attendance per user with minutes >= 1
        if (!Schema::hasTable('attendance') || !Schema::hasColumn('attendance','user_id')) {
            return back()->with('error', 'Attendance table missing.');
        }

        $hasOut = Schema::hasColumn('attendance','check_out');
        $hasIn  = Schema::hasColumn('attendance','check_in');

        if (!$hasIn) return back()->with('error', 'Attendance check_in column missing.');

        $rows = DB::table('attendance')
            ->select('user_id',
                DB::raw($hasOut
                    ? 'SUM(TIMESTAMPDIFF(MINUTE, check_in, check_out)) as minutes'
                    : 'COUNT(*) as minutes'))
            ->where('opportunity_id', $id)
            ->groupBy('user_id')
            ->get();

        $issued = 0;
        foreach ($rows as $r) {
            $minutes = max(0, (int)($r->minutes ?? 0));
            if ($minutes < 1) continue;
            $svc->issueForOpportunityUser($id, (int)$r->user_id, $minutes);
            $issued++;
        }

        return back()->with('status', "Certificates generated: {$issued}");
    }
}
