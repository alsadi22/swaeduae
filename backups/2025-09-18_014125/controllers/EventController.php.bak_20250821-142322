<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $events = Event::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('title', 'like', "%{$q}%")
                       ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('events.index', compact('events', 'q'));
    }

    public function show(string $idOrSlug)
    {
        if (is_numeric($idOrSlug)) {
            $event = Event::findOrFail((int) $idOrSlug);
        } else {
            // Only attempt slug lookup if the column exists; otherwise 404.
            if (! Schema::hasColumn('events', 'slug')) {
                abort(404);
            }
            $event = Event::where('slug', $idOrSlug)->firstOrFail();
        }

        return view('events.show', compact('event'));
    }
    public function sharjah(Request $request)
    {
        return redirect()->route("events.index", ["q" => "Sharjah"]);
    }

}
