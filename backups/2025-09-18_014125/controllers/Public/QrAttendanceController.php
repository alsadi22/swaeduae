<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\QrScan;
use App\Models\VolunteerHour;

class QrAttendanceController extends Controller
{
    public function index(Request $r)
    {
        return view('public.scan', [
            'status' => session('status'),
        ]);
    }

    public function checkin(Request $r)  { return $this->handleScan($r, 'checkin'); }
    public function checkout(Request $r) { return $this->handleScan($r, 'checkout'); }

    protected function handleScan(Request $r, string $action)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $data = $r->validate([
            'code'           => ['nullable','string','max:255'],
            'opportunity_id' => ['nullable','integer'],
            'lat'            => ['nullable','numeric'],
            'lng'            => ['nullable','numeric'],
        ]);

        $scanAt = Carbon::now();

        return DB::transaction(function () use ($data, $user, $action, $scanAt, $r) {

            // 1) persist the raw scan
            $scan = QrScan::create([
                'user_id'        => $user->id,
                'opportunity_id' => $data['opportunity_id'] ?? null,
                'action'         => $action,
                'code'           => $data['code'] ?? null,
                'lat'            => $data['lat'] ?? null,
                'lng'            => $data['lng'] ?? null,
                'ip'             => $r->ip(),
                'ip_address'     => $r->ip(),
                'user_agent'     => substr($r->userAgent() ?? '', 0, 255),
                'scanned_at'     => $scanAt,
            ]);

            // 2) if this is a checkout, pair with the latest unmatched checkin and record hours
            if ($action === 'checkout') {
                $checkin = QrScan::query()
                    ->where('user_id', $user->id)
                    ->when($data['opportunity_id'] ?? null, fn($q,$oid) => $q->where('opportunity_id',$oid))
                    ->where('action','checkin')
                    ->whereNull('attendance_id')
                    ->orderByDesc('scanned_at')
                    ->first();

                if ($checkin) {
                    $start = $checkin->scanned_at ?? $checkin->created_at ?? $scanAt;
                    $end   = $scanAt;

                    $mins = max(1, (int) $start->diffInMinutes($end));
                    $mins = min($mins, 16*60); // cap one session at 16h
                    $hours = round($mins / 60, 2);

                    $vh = VolunteerHour::create([
                        'user_id'        => $user->id,
                        'opportunity_id' => $data['opportunity_id'] ?? null,
                        'hours'          => $hours,
                        'note'           => 'QR session '.$start->toDateTimeString().' → '.$end->toDateTimeString().' ('.$mins.'m)',
                    ]);

                    // link both scans to this session id (reuse attendance_id column as a link)
                    $checkin->update(['attendance_id' => $vh->id]);
                    $scan->update(['attendance_id' => $vh->id]);

                    return back()->with('status', 'Checkout recorded at '.$end->format('H:i:s').' — '.$mins.' min ('.$hours.' h) saved.');
                }

                // no matching check-in, still acknowledge the checkout
                return back()->with('status', 'Checkout recorded at '.$scanAt->format('H:i:s').' (no open check-in found).');
            }

            // check-in path
            return back()->with('status', 'Checkin recorded at '.$scanAt->format('H:i:s'));
        });
    }
}
