<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class EventPhotoController extends Controller
{
    public function show(int $eventId)
    {
        $event = Event::findOrFail($eventId);

        if (view()->exists('admin.events.photos')) {
            return view('admin.events.photos', compact('event'));
        }

        return response('Event photos page', 200);
    }

    public function store(Request $request, int $eventId): RedirectResponse
    {
        $event = Event::findOrFail($eventId);

        if ($request->hasFile('photos')) {
            foreach ((array) $request->file('photos') as $photo) {
                // Save to public disk; we’re not mutating DB columns here
                $photo->store('event_photos', 'public');
            }
        }

        return back()->with('status', 'Photos uploaded');
    }
}
