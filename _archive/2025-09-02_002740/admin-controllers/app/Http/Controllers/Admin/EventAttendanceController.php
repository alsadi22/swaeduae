<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventAttendanceController extends Controller
{
    public function showQr($event_id) {
        $event = Event::findOrFail($event_id);
        $qr = base64_encode(\QrCode::format('png')->size(200)->generate(route('event.attendance', $event->id)));
        return view('admin.events.qr', compact('event', 'qr'));
    }
}
