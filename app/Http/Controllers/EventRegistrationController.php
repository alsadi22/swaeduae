<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventRegistrationController extends Controller
{
    public function join(Event $event)
    {
        $userId = Auth::id();
        // Upsert to be safe if user double-clicks
        DB::table('event_user')->updateOrInsert(
            ['event_id' => $event->id, 'user_id' => $userId],
            ['joined_at' => now(), 'updated_at' => now(), 'created_at' => now()]
        );
        return back()->with('ok', __('messages.joined_event') ?? 'Joined the event.');
    }

    public function unjoin(Event $event)
    {
        $userId = Auth::id();
        DB::table('event_user')->where(['event_id' => $event->id, 'user_id' => $userId])->delete();
        return back()->with('ok', __('messages.left_event') ?? 'Left the event.');
    }
}
