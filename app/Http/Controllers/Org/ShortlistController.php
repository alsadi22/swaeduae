<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Opportunity;

class ShortlistController extends Controller
{
    private function appTable(): string
    {
        foreach (['applications','opportunity_applications','applications_opportunities'] as $t) {
            if (Schema::hasTable($t)) return $t;
        }
        return 'applications';
    }

    public function index($opportunityId)
    {
        $opportunity = Opportunity::findOrFail($opportunityId);
        $this->authorizeOpportunity($opportunity);

        $table = $this->appTable();

        $q = DB::table($table.' as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->select('a.*', 'u.name as user_name', 'u.email as user_email')
            ->where('a.opportunity_id', $opportunity->id);

        if ($s = request('s')) {
            $q->where(function($x) use ($s) {
                $x->where('u.name','like',"%$s%")
                  ->orWhere('u.email','like',"%$s%");
            });
        }
        if ($st = request('status')) {
            $q->where('a.status', $st);
        }

        $apps = $q->orderBy('a.created_at')->paginate(25)->withQueryString();

        // Stats
        $counts = DB::table($table.' as a')
            ->selectRaw("count(*) as total,
                sum(case when status='approved' then 1 else 0 end) as approved,
                sum(case when status='waitlist' then 1 else 0 end) as waitlist,
                sum(case when status='rejected' then 1 else 0 end) as rejected,
                sum(case when status='pending' then 1 else 0 end) as pending")
            ->where('a.opportunity_id', $opportunity->id)
            ->first();

        $approved = (int)($counts->approved ?? 0);
        $cap = $opportunity->slot_cap;
        $available = is_null($cap) ? '∞' : max(0, (int)$cap - $approved);

        return view('org.opportunities.shortlist', [
            'opportunity' => $opportunity,
            'apps' => $apps,
            'counts' => $counts,
            'available' => $available,
            'cap' => $cap,
        ]);
    }

    public function bulk(Request $request, $opportunityId)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'action' => 'required|string|in:approve,waitlist,reject,pending',
        ]);

        $opportunity = Opportunity::findOrFail($opportunityId);
        $this->authorizeOpportunity($opportunity);

        $table = $this->appTable();
        $ids = array_map('intval', $request->ids);
        $action = $request->action;
        $now = Carbon::now();

        DB::transaction(function () use ($table, $ids, $action, $opportunity, $now) {
            DB::table($table)
                ->whereIn('id', $ids)
                ->where('opportunity_id', $opportunity->id)
                ->update([
                    'status' => $action === 'pending' ? 'pending' : $action,
                    'status_changed_at' => $now,
                    'updated_at' => $now,
                ]);

            $this->enforceSlotCap($opportunity, $table);
        });

        $this->audit('shortlist.bulk', [
            'action' => $action,
            'ids' => $ids,
            'opportunity_id' => $opportunity->id,
        ], $opportunity->id);

        return back()->with('status', __('Shortlist updated.'));
    }

    public function updateSlotCap(Request $request, $opportunityId)
    {
        $request->validate(['slot_cap' => 'nullable|integer|min:0']);

        $opportunity = Opportunity::findOrFail($opportunityId);
        $this->authorizeOpportunity($opportunity);

        $opportunity->slot_cap = $request->slot_cap === null ? null : (int)$request->slot_cap;
        $opportunity->save();

        $table = $this->appTable();
        DB::transaction(function () use ($opportunity, $table) {
            $this->enforceSlotCap($opportunity, $table);
        });

        $this->audit('shortlist.slot_cap.update', [
            'slot_cap' => $opportunity->slot_cap,
            'opportunity_id' => $opportunity->id,
        ], $opportunity->id);

        return back()->with('status', __('Slot cap updated.'));
    }

    private function enforceSlotCap($opportunity, string $table): void
    {
        $cap = $opportunity->slot_cap;
        if ($cap === null) return; // unlimited

        // Count current approved
        $approvedCount = (int) DB::table($table)
            ->where('opportunity_id', $opportunity->id)
            ->where('status', 'approved')
            ->count();

        if ($approvedCount > $cap) {
            // Demote the latest approvals back to waitlist until under cap
            $toDemote = $approvedCount - $cap;
            $ids = DB::table($table)
                ->where('opportunity_id', $opportunity->id)
                ->where('status', 'approved')
                ->orderByDesc(DB::raw('COALESCE(status_changed_at, updated_at, created_at)'))
                ->limit($toDemote)
                ->pluck('id');

            if ($ids->count()) {
                DB::table($table)->whereIn('id', $ids)->update([
                    'status' => 'waitlist',
                    'status_changed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        } elseif ($approvedCount < $cap) {
            // Promote earliest waitlisted into approved until capacity reached
            $need = $cap - $approvedCount;
            $ids = DB::table($table)
                ->where('opportunity_id', $opportunity->id)
                ->where('status', 'waitlist')
                ->orderBy(DB::raw('COALESCE(status_changed_at, created_at)'))
                ->limit($need)
                ->pluck('id');

            if ($ids->count()) {
                DB::table($table)->whereIn('id', $ids)->update([
                    'status' => 'approved',
                    'status_changed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

    private function authorizeOpportunity($opportunity): void
    {
        $user = Auth::user();
        if (property_exists($opportunity, 'organization_id')) {
            if ((int)$opportunity->organization_id !== (int)($user->organization_id ?? 0)) {
                abort(403, 'Unauthorized org scope.');
            }
        }
    }

    private function audit(string $action, array $meta, int $opportunityId): void
    {
        try {
            DB::table('audit_logs')->insert([
                'action' => $action,
                'meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
                'user_id' => Auth::id(),
                'opportunity_id' => $opportunityId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } catch (\Throwable $e) {
            // swallow
        }
    }
}
