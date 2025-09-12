<?php
namespace App\Http\Controllers;

use App\Models\EventApplication;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventApplicationController extends Controller
{
    public function apply(Request $request, Event $event)
    {
        $request->validate([
            'additional_info' => 'nullable|array',
            'additional_info.*' => 'string|max:255',
        ]);

        $application = EventApplication::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => Auth::id()],
            ['status' => 'pending', 'additional_info' => $request->additional_info]
        );

        return redirect()->route('events.show', ['idOrSlug' => ($event->slug ?? $event->getKey())])->with('success', 'Application submitted.');
    }

    public function withdraw(Event $event)
    {
        EventApplication::where('event_id', $event->id)->where('user_id', Auth::id())->delete();

        return redirect()->route('dashboard')->with('success', 'Application withdrawn.');
    }
}
