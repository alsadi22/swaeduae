<?php
namespace App\Http\Controllers;

use App\Models\Event;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventQrController extends Controller
{
    public function show(Event $event)
    {
        $qr = QrCode::size(200)->generate(route('events.checkin', $event));
        return view('events.qr', compact('event', 'qr'));
    }
}
