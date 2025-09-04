<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'active')->orderBy('date', 'asc')->get();
        return view('events.index', compact('events'));
    }

    public function register(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $user = Auth::user();
        if (!$user->events()->where('event_id', $id)->exists()) {
            $user->events()->attach($event);
            return redirect()->route('profile')->with('success', __('Successfully registered for the event!'));
        }
        return redirect()->route('profile')->with('error', __('You have already registered for this event.'));
    }
}
