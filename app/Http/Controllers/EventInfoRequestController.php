<?php
namespace App\Http\Controllers;

use App\Models\EventInfoRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventInfoRequestController extends Controller
{
    public function show(Event $event)
    {
        $infoRequest = EventInfoRequest::where('event_id', $event->id)->first();
        return view('events.info_request', compact('event', 'infoRequest'));
    }

    public function submit(Request $request, Event $event)
    {
        // Validate and process submitted info request data here

        // For simplicity, just redirect with success message
        return redirect()->route('events.show', ['idOrSlug' => ($event->slug ?? $event->getKey())])->with('success', 'Information submitted.');
    }
}
