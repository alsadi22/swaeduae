<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Opportunity;
use App\Models\User;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Opportunity $opportunity)
    {
        $this->authorize('admin'); // Gate via middleware; extra guard if policy exists
        $attendances = Attendance::with('user')
            ->where('opportunity_id', $opportunity->id)
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin.attendance.index', compact('opportunity', 'attendances'));
    }

    public function update(Request $request, Opportunity $opportunity, Attendance $attendance)
    {
        $request->validate([
            'minutes'  => 'nullable|integer|min:0|max:100000',
            'no_show'  => 'nullable|boolean',
            'notes'    => 'nullable|string|max:5000',
            'check_in_at'  => 'nullable|date',
            'check_out_at' => 'nullable|date|after_or_equal:check_in_at',
        ]);

        $attendance->minutes     = $request->filled('minutes') ? (int)$request->minutes : $attendance->minutes;
        $attendance->no_show     = (bool)$request->input('no_show', $attendance->no_show);
        if ($request->filled('notes')) $attendance->notes = $request->notes;
        if ($request->filled('check_in_at'))  $attendance->check_in_at  = Carbon::parse($request->check_in_at);
        if ($request->filled('check_out_at')) $attendance->check_out_at = Carbon::parse($request->check_out_at);
        if (!$attendance->minutes && $attendance->check_in_at && $attendance->check_out_at) {
            $attendance->minutes = $attendance->check_in_at->diffInMinutes($attendance->check_out_at);
        }
        $attendance->save();

        // Recalculate user's total minutes (simple sum of attendance)
        $totalMinutes = Attendance::where('user_id', $attendance->user_id)->whereNull('deleted_at')->sum('minutes');
        DB::table('users')->where('id', $attendance->user_id)->update(['total_minutes_cached' => $totalMinutes]); // optional cache field

        return back()->with('status', __('messages.attendance_updated'));
    }

    public function finalizeIssue(Opportunity $opportunity, CertificateService $certs)
    {
        // Issue certificates for completed (non no-show) attendances
        $issued = 0;
        $attendances = Attendance::with('user')
            ->where('opportunity_id', $opportunity->id)
            ->where('no_show', false)
            ->get();

        foreach ($attendances as $a) {
            if (!$a->user) continue;

            $minutes = $a->minutes;
            if (!$minutes && $a->check_in_at && $a->check_out_at) {
                $minutes = $a->check_in_at->diffInMinutes($a->check_out_at);
            }
            if (!$minutes || $minutes <= 0) continue;

            $hours = round($minutes / 60, 2);

            // Skip if a certificate for this user+event already exists
            $found = Certificate::where('user_id', $a->user_id)->where('event_id', $opportunity->id)->first();
            if ($found) continue;

            $cert = $certs->generate($a->user, $opportunity, $hours);
            $certs->sendEmail($cert);
            $issued++;
        }

        return back()->with('status', __('messages.certificates_issued', ['count' => $issued]));
    }

    public function resendCertificate(Certificate $certificate, CertificateService $certs)
    {
        $certs->sendEmail($certificate);
        return back()->with('status', __('messages.certificate_resent'));
    }
}
