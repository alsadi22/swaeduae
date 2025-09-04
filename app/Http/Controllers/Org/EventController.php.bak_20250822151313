<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    protected function orgIdsForUser($user): array
    {
        $ids = [];
        if (Schema::hasTable('organizations')) {
            if (Schema::hasColumn('organizations', 'owner_user_id')) {
                $ids = DB::table('organizations')->where('owner_user_id', $user->id)->pluck('id')->toArray();
            }
            if (!$ids && Schema::hasColumn('organizations', 'user_id')) {
                $ids = DB::table('organizations')->where('user_id', $user->id)->pluck('id')->toArray();
            }
        }
        if (!$ids && Schema::hasTable('organization_user')) {
            $ids = DB::table('organization_user')->where('user_id', $user->id)->pluck('organization_id')->toArray();
        }
        return array_values(array_unique(array_map('intval', $ids)));
    }

    protected function detectOrgFk(): ?string
    {
        if (!Schema::hasTable('opportunities')) return null;
        $cols = Schema::getColumnListing('opportunities');
        foreach (['organization_id','org_id','organizationId','orgId','owner_org_id'] as $c) {
            if (in_array($c, $cols, true)) return $c;
        }
        return null;
    }

    protected function assertOwnsOpportunity(int $opportunityId, array $orgIds, ?string $orgFk): void
    {
        if (!$orgFk) {
            // No FK column to assert ownership; allow for now (MVP)
            return;
        }
        $belongs = DB::table('opportunities')
            ->where('id', $opportunityId)
            ->whereIn($orgFk, $orgIds)
            ->exists();
        abort_unless($belongs, 403, 'This opportunity does not belong to your organization.');
    }

    public function index(Request $request)
    {
        $user   = Auth::user();
        $orgIds = $this->orgIdsForUser($user);
        abort_if(empty($orgIds), 403, 'No organization assigned to this user.');

        $orgFk = $this->detectOrgFk();

        $orderCol = Schema::hasColumn('opportunities','start_date') ? 'start_date'
                 : (Schema::hasColumn('opportunities','starts_at') ? 'starts_at' : 'id');

        $q = DB::table('opportunities');

        $notice = null;
        if ($orgFk) {
            $q->whereIn($orgFk, $orgIds);
        } elseif (Schema::hasColumn('opportunities','created_by')) {
            $q->where('created_by', $user->id);
            $notice = 'Showing opportunities created by you (no org link column found).';
        } else {
            $q->whereRaw('1=0');
            $notice = 'No organization link column on opportunities; nothing to show yet.';
        }

        if ($s = trim((string) $request->get('q'))) {
            $q->where(function($w) use ($s) {
                $w->where('title','like',"%$s%")->orWhere('description','like',"%$s%");
            });
        }

        $opps = $q->orderByDesc($orderCol)->paginate(15)->withQueryString();

        return view('org.events.index', compact('opps') + ['notice'=>$notice]);
    }

    public function volunteers(Request $request, int $opportunity)
    {
        $user   = Auth::user();
        $orgIds = $this->orgIdsForUser($user);
        $orgFk  = $this->detectOrgFk();
        $this->assertOwnsOpportunity($opportunity, $orgIds, $orgFk);

        $rows = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.opportunity_id', $opportunity)
            ->selectRaw("
                a.id, a.user_id, u.name,
                a.check_in_at, a.checked_in_at, a.checkin_at,
                a.check_out_at, a.checked_out_at, a.checkout_at,
                a.minutes, a.hours,
                COALESCE(a.minutes, IFNULL(a.hours,0)*60,
                         TIMESTAMPDIFF(MINUTE, COALESCE(a.check_in_at, a.checked_in_at, a.checkin_at),
                                               COALESCE(a.check_out_at, a.checked_out_at, a.checkout_at))) as computed_minutes
            ")
            ->orderBy('a.id')
            ->paginate(25)
            ->withQueryString();

        if(request()->boolean('partial')){ return view('org.events._volunteers_table', ['rows' => ($rows ?? []), 'opportunityId' => ($opportunity ?? $opportunityId ?? request()->input('opportunity_id'))]); }
        return view('org.events.volunteers', ['rows' => ($rows ?? []), 'opportunityId' => ($opportunity ?? $opportunityId ?? request()->input('opportunity_id'))]);
    }

    public function export(Request $request, int $opportunity)
    {
        $user   = Auth::user();
        $orgIds = $this->orgIdsForUser($user);
        $orgFk  = $this->detectOrgFk();
        $this->assertOwnsOpportunity($opportunity, $orgIds, $orgFk);

        $filename = "event_{$opportunity}_volunteers_" . date('Ymd_His') . ".csv";

        $rows = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.opportunity_id', $opportunity)
            ->selectRaw("
                u.id as user_id, u.name, u.email,
                a.id as attendance_id,
                COALESCE(a.check_in_at, a.checked_in_at, a.checkin_at) as check_in,
                COALESCE(a.check_out_at, a.checked_out_at, a.checkout_at) as check_out,
                COALESCE(a.minutes, IFNULL(a.hours,0)*60,
                         TIMESTAMPDIFF(MINUTE, COALESCE(a.check_in_at, a.checked_in_at, a.checkin_at),
                                               COALESCE(a.check_out_at, a.checked_out_at, a.checkout_at))) as minutes
            ")
            ->orderBy('u.name')
            ->get();

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['user_id','name','email','attendance_id','check_in','check_out','minutes']);
            foreach ($rows as $r) {
                fputcsv($out, [(int)$r->user_id, $r->name, $r->email, (int)$r->attendance_id, $r->check_in, $r->check_out, (int)$r->minutes]);
            }
            fclose($out);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");
        return $response;
    }

    public function updateMinutes(Request $request, int $attendance)
    {
        $request->validate(['minutes' => 'required|integer|min:0|max:2000']);

        $user   = Auth::user();
        $orgIds = $this->orgIdsForUser($user);
        $orgFk  = $this->detectOrgFk();

        $a = DB::table('attendances')->where('id', $attendance)->first();
        abort_unless($a, 404, 'Attendance not found');

        $this->assertOwnsOpportunity((int)$a->opportunity_id, $orgIds, $orgFk);

        DB::table('attendances')->where('id', $attendance)->update([
            'minutes'    => (int)$request->minutes,
            'updated_at' => now(),
]);

    $expected = (int) DB::table('attendances')
        ->where('user_id', $a->user_id)
        ->where('opportunity_id', $a->opportunity_id)
        ->selectRaw("COALESCE(SUM(COALESCE(minutes, IFNULL(hours,0)*60, TIMESTAMPDIFF(MINUTE, COALESCE(check_in_at, checked_in_at, checkin_at), COALESCE(check_out_at, checked_out_at, checkout_at)))), 0) as s")
        ->value('s');

    $hours = intdiv($expected, 60);

        $existing = DB::table('volunteer_hours')
            ->where('user_id', $a->user_id)
            ->where('opportunity_id', $a->opportunity_id)
            ->first();

        if ($existing) {
            DB::table('volunteer_hours')->where('id', $existing->id)->update([
                'minutes'    => $expected,
                'hours'      => $hours,
                'source'     => 'org-edit',
                'updated_at' => now(),
]);
        } else {
            DB::table('volunteer_hours')->insert([
                'user_id'        => $a->user_id,
                'opportunity_id' => $a->opportunity_id,
                'minutes'        => $expected,
                'hours'          => $hours,
                'source'         => 'org-edit',
                'created_at'     => now(),
                'updated_at'     => now(),
]);
        }

        return back()->with('status', 'Updated minutes.');
    }
}
