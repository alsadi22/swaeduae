<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Opportunity;
use App\Models\User;
use App\Models\Certificate;
use App\Models\AuditLog;
use App\Services\CertificateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateIssuedMail;

class CertificateAdminController extends Controller
{
    public function bulkForm()
    {
        $opps = Opportunity::orderByDesc('id')->limit(200)->get(['id','title']);
        return view('admin.certificates.bulk_issue', compact('opps'));
    }

    public function bulkIssue(Request $request, CertificateService $svc)
    {
        $request->validate(['opportunity_id' => 'required|integer|exists:opportunities,id']);
        $opp = Opportunity::findOrFail((int)$request->input('opportunity_id'));

        // strategy: for all users who have hours or attendance for this opportunity, ensure a certificate exists
        $userIds = DB::table('volunteer_hours')->where('opportunity_id', $opp->id)->pluck('user_id')->unique()->values();
        // fallback: from attendance
        $attUserIds = DB::table('attendances')->where('opportunity_id', $opp->id)->pluck('user_id')->unique()->values();
        $userIds = $userIds->merge($attUserIds)->unique()->values();

        $results = ['created'=>0,'updated'=>0,'errors'=>0, 'skipped'=>0, 'list'=>[]];

        foreach ($userIds as $uid) {
            $user = User::find($uid);
            if (!$user) { $results['skipped']++; continue; }

            $hours = (float) DB::table('volunteer_hours')
                        ->where(['user_id'=>$uid,'opportunity_id'=>$opp->id])
                        ->sum('minutes') / 60.0;

            try {
                $existing = Certificate::where(['user_id'=>$uid,'opportunity_id'=>$opp->id])->first();
                $cert = $svc->issueFor($user, $opp, $hours);
                $results[$existing ? 'updated' : 'created']++;
                $results['list'][] = $cert->code ?? $cert->verification_code;
                // small throttle to be gentle with SMTP providers
                usleep(150000);
            } catch (\Throwable $e) {
                $results['errors']++;
            }
        }

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'bulk_issue',
            'target_type' => 'opportunity',
            'target_id' => $opp->id,
            'meta' => $results,
        ]);

        return view('admin.certificates.bulk_issue_result', compact('opp','results'));
    }

    public function resendForm()
    {
        return view('admin.certificates.resend');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'opportunity_id' => 'nullable|integer',
        ]);

        $q = Certificate::query()->with(['user','opportunity']);

        if ($request->filled('code')) {
            $q->where('code', $request->code)->orWhere('verification_code', $request->code);
        } elseif ($request->filled('user_id') && $request->filled('opportunity_id')) {
            $q->where('user_id', (int)$request->user_id)->where('opportunity_id', (int)$request->opportunity_id);
        } else {
            return back()->with('error', 'Provide code or (user_id + opportunity_id).');
        }

        $cert = $q->firstOrFail();

        try {
            Mail::to($cert->user->email)->send(new \App\Mail\CertificateIssuedMail($cert));
            $ok = true;
        } catch (\Throwable $e) {
            $ok = false;
        }

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'resend_certificate',
            'target_type' => 'certificate',
            'target_id' => $cert->id,
            'meta' => ['code' => $cert->code ?? $cert->verification_code, 'ok' => $ok],
        ]);

        return back()->with('status', $ok ? 'Resent' : 'Failed to send');
    }
}
