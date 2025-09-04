<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\QrScan;
use Carbon\Carbon;

class QrScanController extends Controller
{
    /**
     * Handle QR scan via GET request.
     */
    public function scan(Request $request, Attendance $attendance, $action)
    {
        // Log scan
        QrScan::create([
            'attendance_id' => $attendance->id,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        $message = '';
        $status = 'success';

        if ($action === 'checkin') {
            if (is_null($attendance->check_in)) {
                $attendance->check_in = Carbon::now();
                $message = __('You have successfully checked in for this event.');
            } else {
                $message = __('You have already checked in.');
                $status = 'info';
            }
        } elseif ($action === 'checkout') {
            if (!is_null($attendance->check_in) && is_null($attendance->check_out)) {
                $attendance->check_out = Carbon::now();
                $message = __('You have successfully checked out from this event.');
            } else {
                $message = __('You have already checked out.');
                $status = 'info';
            }
        } else {
            $message = __('Invalid action.');
            $status = 'danger';
        }

        $attendance->save();

        return view('qr.scan-result', [
            'status' => $status,
            'message' => $message,
            'attendance' => $attendance
        ]);
    }
}
