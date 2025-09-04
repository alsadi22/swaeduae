<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Opportunity;
use App\Models\Attendance;
use App\Models\Organization;
use App\Models\Certificate;
use App\Services\IssueCertificates;

class OpportunityAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    protected function userHasOrgAccess(Opportunity $op): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        if (!empty($user->is_admin)) return true;

        $ownedOrgIds = Organization::query()
            ->where('owner_user_id', $user->id)->pluck('id')->all();

        $directOrgId = $user->organization_id ?? null;

        return in_array($op->organization_id, $ownedOrgIds, true) || ($directOrgId && $op->organization_id == $directOrgId);
    }

    protected function denyIfNoAccess(Opportunity $op)
    {
        if (!$this->userHasOrgAccess($op)) abort(403, __('You do not have access to this organization.'));
    }

    // GET /org/opportunities/{opportunity}/attendance
    public function index(Opportunity $opportunity)
    {
        $this->denyIfNoAccess($opportunity);

        $attendances = Attendance::query()
            ->when(Schema::hasColumn('attendances','opportunity_id'), fn($q)=>$q->where('opportunity_id', $opportunity->id))
            ->with(['user' => function($q){ $q->select('id','name','email'); }])
            ->orderByDesc('id')
            ->paginate(50);

        return view('org.attendance.index', ['event' => $opportunity, 'attendances' => $attendances]);
    }

    // POST /org/opportunities/{opportunity}/finalize
    public function finalize(Opportunity $opportunity, Request $request, IssueCertificates $issuer)
    {
        $this->denyIfNoAccess($opportunity);

        $countIssued = 0;

        DB::transaction(function () use ($opportunity, $issuer, &$countIssued) {
            // Optional: mark finalized if these columns exist on opportunities
            if (Schema::hasColumn('opportunities','finalized_at')) {
                $opportunity->finalized_at = now();
            }
            if (Schema::hasColumn('opportunities','finalized_by')) {
                $opportunity->finalized_by = Auth::id();
            }
            try { $opportunity->save(); } catch (\Throwable $e) { /* ignore if cols don't exist */ }

            $attendances = Attendance::query()
                ->when(Schema::hasColumn('attendances','opportunity_id'), fn($q)=>$q->where('opportunity_id', $opportunity->id))
                ->get();

            foreach ($attendances as $att) {
                $cert = $issuer->issueForAttendance($att);
                if ($cert) $countIssued++;
            }

            $this->logAudit($opportunity->id, 'opportunity.finalize', [
                'issued'     => $countIssued,
                'by_user_id' => Auth::id(),
            ]);
        });

        return back()->with('status', "Finalized. Certificates issued: {$countIssued}");
    }

    // Reuse existing AttendanceController endpoints for check/no-show/minutes/resend
    // since they operate on Attendance rows directly

    protected function logAudit(int $opportunityId, string $action, array $meta = []): void
    {
        try {
            if (!Schema::hasTable('audit_logs')) return;
            DB::table('audit_logs')->insert([
                'action'     => $action,
                'meta'       => json_encode($meta, JSON_UNESCAPED_UNICODE),
                'user_id'    => Auth::id(),
                'event_id'   => $opportunityId, // using same column for compatibility
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('audit_logs insert failed: '.$e->getMessage());
        }
    }
}
