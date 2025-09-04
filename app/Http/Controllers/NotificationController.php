<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $notifications = $user->notifications()->latest()->paginate(20);
        $unreadCount   = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications','unreadCount'));
    }

    public function markRead(Request $request, string $id)
    {
        $n = $request->user()->notifications()->whereKey($id)->firstOrFail();
        if ($n->read_at === null) {
            $n->markAsRead();
        }
        return back();
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }

    // simple test helper to verify delivery
    public function test(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $user->notify(new \App\Notifications\CertificateIssued([
            'title' => 'Test notification',
            'body'  => 'This is a test notification from P9 setup.',
        ]));

        return redirect()->route('notifications.index')->with('status', 'Test notification sent.');
    }
}
