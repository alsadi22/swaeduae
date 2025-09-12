<?php
namespace App\Http\Controllers;

use App\Models\EventEvaluation;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventEvaluationController extends Controller
{
    public function create(Event $event)
    {
        return view('events.evaluation', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
            'reflection' => 'required|string|max:2000',
        ]);

        EventEvaluation::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => Auth::id()],
            $request->only('rating', 'comments', 'reflection')
        );

        return redirect()->route('dashboard')->with('success', 'Thank you for your feedback.');
    }
}
