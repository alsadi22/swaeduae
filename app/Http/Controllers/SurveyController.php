<?php
namespace App\Http\Controllers;

use App\Models\EventEvaluation;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    public function create(Event $event)
    {
        return view('surveys.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'responses' => 'required|array',
            'responses.*' => 'string|max:1000',
        ]);

        // Save responses logic here (customize as needed)

        EventEvaluation::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => Auth::id()],
            ['comments' => json_encode($data['responses'])]
        );

        return redirect()->route('dashboard')->with('success', 'Survey submitted.');
    }
}
