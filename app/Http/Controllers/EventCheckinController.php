<?php
namespace App\Http\Controllers;

use App\Models\EventCheckin;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventCheckinController extends Controller
{
    public function checkin(Event $event)
    {
        $checkin = EventCheckin::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => Auth::id()],
            ['checkin_time' => now()]
        );

        return redirect()->route('events.show', ['idOrSlug' => ($event->slug ?? $event->getKey())])->with('success', 'Checked in.');
    }

    public function checkout(Event $event)
    {
        $checkin = EventCheckin::where('event_id', $event->id)->where('user_id', Auth::id())->first();

        if ($checkin && !$checkin->checkout_time) {
            $checkin->checkout_time = now();
            $checkin->save();
        }

        return redirect()->route('events.show', ['idOrSlug' => ($event->slug ?? $event->getKey())])->with('success', 'Checked out.');
    }
}
