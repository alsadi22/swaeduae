<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Opportunity;
use App\Models\User;
use App\Models\VolunteerHour;

class PortalController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $events = Event::where('organization_id', $user->id)->get();
        $opportunities = Opportunity::where('organization_id', $user->id)->get();
        $volunteers = User::where('role', 'volunteer')->whereHas('registrations.event', function($q) use ($user) {
            $q->where('organization_id', $user->id);
        })->get();
        $hours = VolunteerHour::whereIn('event_id', $events->pluck('id'))->get();
        return view('organization.dashboard', compact('user', 'events', 'opportunities', 'volunteers', 'hours'));
    }

    public function createEvent() { return view('organization.events.create'); }
    public function storeEvent(Request $req)
    {
        $user = auth()->user();
        $event = Event::create($req->all() + ['organization_id'=>$user->id]);
        return redirect()->route('organization.dashboard')->with('success', 'Event created!');
    }
    public function editEvent($id) { $event = Event::findOrFail($id); return view('organization.events.edit', compact('event')); }
    public function updateEvent(Request $req, $id)
    {
        $event = Event::findOrFail($id); $event->update($req->all());
        return redirect()->route('organization.dashboard')->with('success', 'Event updated!');
    }
    public function destroyEvent($id)
    {
        Event::destroy($id);
        return redirect()->route('organization.dashboard')->with('success', 'Event deleted!');
    }
    // Opportunity CRUD
    public function createOpportunity() { return view('organization.opportunities.create'); }
    public function storeOpportunity(Request $req)
    {
        $user = auth()->user();
        $opp = Opportunity::create($req->all() + ['organization_id'=>$user->id]);
        return redirect()->route('organization.dashboard')->with('success', 'Opportunity created!');
    }
    public function editOpportunity($id) { $op = Opportunity::findOrFail($id); return view('organization.opportunities.edit', compact('op')); }
    public function updateOpportunity(Request $req, $id)
    {
        $op = Opportunity::findOrFail($id); $op->update($req->all());
        return redirect()->route('organization.dashboard')->with('success', 'Opportunity updated!');
    }
    public function destroyOpportunity($id)
    {
        Opportunity::destroy($id);
        return redirect()->route('organization.dashboard')->with('success', 'Opportunity deleted!');
    }
}
