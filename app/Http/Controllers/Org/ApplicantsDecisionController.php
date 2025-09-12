<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Mail\ApplicantDecisionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class ApplicantsDecisionController extends Controller
{
    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => ['required','in:approved,declined,waitlist'],
            'application_ids' => ['required','array'],
            'application_ids.*' => ['integer'],
            'note' => ['nullable','string','max:2000'],
            'reason' => ['nullable','string','max:2000'],
        ]);

        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        if (!$orgId) return $this->backOrJson(['error'=>'Organization not found'], 422);

        $oppIds = DB::table('opportunities')->where('organization_id',$orgId)->pluck('id');
        if ($oppIds->isEmpty()) return $this->backOrJson(['status'=>'No opportunities found.']);

        $apps = DB::table('applications')
                 ->whereIn('id',$data['application_ids'])
                 ->whereIn('opportunity_id',$oppIds)
                 ->get(['id','user_id','opportunity_id','status']);

        if ($apps->isEmpty()) return $this->backOrJson(['status'=>'No matching applications.']);

        $updatesById = [];
        $affectedOpps = [];
        $action = $data['action'];

        if ($action === 'approved') {
            // Enforce per-opportunity cap
            $grouped = $apps->groupBy('opportunity_id');
            foreach ($grouped as $oppId => $rows) {
                $cap = $this->getSlotCap($oppId);
                if (!$cap) {
                    foreach ($rows as $r) $updatesById[$r->id] = 'approved';
                    $affectedOpps[$oppId] = true;
                    continue;
                }
                $alreadyApproved = (int) DB::table('applications')
                    ->where('opportunity_id',$oppId)->where('status','approved')->count();
                foreach ($rows as $r) {
                    if ($alreadyApproved < $cap) {
                        $updatesById[$r->id] = 'approved';
                        $alreadyApproved++;
                    } else {
                        $updatesById[$r->id] = 'waitlist';
                    }
                }
                $affectedOpps[$oppId] = true;
            }
        } else {
            foreach ($apps as $r) { $updatesById[$r->id] = $action; $affectedOpps[$r->opportunity_id]=true; }
        }

        if (!$updatesById) return $this->backOrJson(['status'=>'Nothing to update.']);

        $ids = array_keys($updatesById);
        $updates = ['updated_at'=>now()];
        if (Schema::hasColumn('applications','decided_at')) $updates['decided_at'] = now();

        // Store reason if column exists
        $hasReason = Schema::hasColumn('applications','decision_reason');

        foreach (array_chunk($ids, 1000) as $chunk) {
            foreach ($chunk as $id) {
                $row = ['status'=>$updatesById[$id]] + $updates;
                if ($hasReason && !empty($data['reason'])) $row['decision_reason'] = $data['reason'];
                DB::table('applications')->where('id',$id)->update($row);
            }
        }

        // Send emails (brand-aware)
        $org = DB::table('organizations')->where('id',$orgId)->first();
        $orgName = $org->name ?? config('app.name');
        $brandColor = $org->primary_color ?? DB::table('settings')->where('key',"org:{$orgId}:primary_color")->value('value');
        $logo = $org->logo_path ?? DB::table('settings')->where('key',"org:{$orgId}:logo_path")->value('value');

        foreach ($apps as $app) {
            if (!isset($updatesById[$app->id])) continue;
            $user = DB::table('users')->where('id',$app->user_id)->first();
            $opp  = DB::table('opportunities')->where('id',$app->opportunity_id)->first();
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new ApplicantDecisionMail(
                        $updatesById[$app->id], $opp->title ?? ('#'.$opp->id), $orgName, $data['note'] ?? null, $brandColor, $logo
                    ));
                } catch (\Throwable $e) { /* ignore */ }
            }
        }

        // Audit
        DB::table('audit_logs')->insert([
            'actor_id'    => Auth::id(),
            'action'      => 'applications.bulk_decision',
            'entity_type' => 'application',
            'entity_id'   => 0,
            'note'        => json_encode(['ids' => $ids, 'action' => $action, 'reason'=>$data['reason'] ?? null]),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Build counters summary
        $summary = [];
        foreach (array_keys($affectedOpps) as $oppId) {
            $summary[$oppId] = [
                'shortlisted' => (int) DB::table('applications')->where('opportunity_id',$oppId)->where('status','shortlisted')->count(),
                'approved'    => (int) DB::table('applications')->where('opportunity_id',$oppId)->where('status','approved')->count(),
                'pending'     => (int) DB::table('applications')->where('opportunity_id',$oppId)->where('status','pending')->count(),
                'waitlist'    => (int) DB::table('applications')->where('opportunity_id',$oppId)->where('status','waitlist')->count(),
                'cap'         => $this->getSlotCap($oppId),
            ];
        }

        return $this->backOrJson(['status'=>__('Updated :n applications.', ['n'=>count($ids)]), 'summary'=>$summary]);
    }

    public function single(Request $request, int $application)
    {
        $data = $request->validate([
            'action' => ['required','in:approved,declined,waitlist'],
            'note'   => ['nullable','string','max:2000'],
            'reason' => ['nullable','string','max:2000'],
        ]);

        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        if (!$orgId) return back()->withErrors(['org' => 'Organization not found']);

        $app = DB::table('applications')->where('id',$application)->first();
        if (!$app) return back()->withErrors(['app' => 'Application not found']);
        $opp = DB::table('opportunities')->where('id',$app->opportunity_id)->first();
        if (!$opp || (int)$opp->organization_id !== (int)$orgId) {
            return back()->withErrors(['auth' => 'Not authorized for this application']);
        }

        // If approving, respect cap
        $action = $data['action'];
        if ($action === 'approved') {
            $cap = $this->getSlotCap($opp->id);
            if ($cap) {
                $already = (int) DB::table('applications')->where('opportunity_id',$opp->id)->where('status','approved')->count();
                if ($already >= $cap) $action = 'waitlist';
            }
        }

        $updates = ['status' => $action, 'updated_at' => now()];
        if (Schema::hasColumn('applications','decided_at')) $updates['decided_at'] = now();
        if (Schema::hasColumn('applications','decision_reason') && !empty($data['reason'])) $updates['decision_reason'] = $data['reason'];

        DB::table('applications')->where('id',$app->id)->update($updates);

        $user = DB::table('users')->where('id',$app->user_id)->first();
        $org  = DB::table('organizations')->where('id',$orgId)->first();
        $orgName = $org->name ?? config('app.name');
        $brandColor = $org->primary_color ?? DB::table('settings')->where('key',"org:{$orgId}:primary_color")->value('value');
        $logo = $org->logo_path ?? DB::table('settings')->where('key',"org:{$orgId}:logo_path")->value('value');

        if ($user && $user->email) {
            try {
                Mail::to($user->email)->send(new ApplicantDecisionMail(
                    $action, $opp->title ?? ('#'.$opp->id), $orgName, $data['note'] ?? null, $brandColor, $logo
                ));
            } catch (\Throwable $e) { /* ignore */ }
        }

        DB::table('audit_logs')->insert([
            'actor_id'    => Auth::id(),
            'action'      => 'applications.single_decision',
            'entity_type' => 'application',
            'entity_id'   => $app->id,
            'note'        => json_encode(['action' => $action, 'reason'=>$data['reason'] ?? null]),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('status', __('Application updated.'));
    }

    private function getSlotCap(int $oppId): ?int
    {
        if (Schema::hasColumn('opportunities','slot_cap')) {
            $v = DB::table('opportunities')->where('id',$oppId)->value('slot_cap');
            if ($v !== null && $v !== '') return (int)$v;
        }
        $sv = DB::table('settings')->where('key',"opp:{$oppId}:slot_cap")->value('value');
        return ($sv !== null && $sv !== '') ? (int)$sv : null;
    }

    private function backOrJson(array $payload, int $code = 200)
    {
        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json($payload, $code);
        }
        if (isset($payload['error'])) return back()->withErrors(['bulk' => $payload['error']])->withInput();
        return back()->with('status', $payload['status'] ?? 'OK');
    }
}
