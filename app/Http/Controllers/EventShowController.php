<?php

namespace App\Http\Controllers;

use App\Models\Event;

class EventShowController extends Controller
{
    public function __invoke($idOrSlug)
    {
        if (is_numeric($idOrSlug)) {
            $event = Event::findOrFail($idOrSlug);

            if (!empty($event->slug)) {
                // 301 to the canonical slug URL
                return redirect()->route('events.show', ['idOrSlug' => $event->slug], 301);
            }

            return view('events.show', compact('event'));
        }

        $event = Event::where('slug', $idOrSlug)->firstOrFail();
        return view('events.show', compact('event'));
    }
}
