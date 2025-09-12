#!/bin/bash

# ====== Add missing Blade views ======

mkdir -p resources/views/events
cat > resources/views/events/show.blade.php << 'EOV'
@extends('layouts.app')

@section('content')
<h1>{{ $event->title ?? 'Event Details' }}</h1>
<p>{{ $event->description ?? '' }}</p>
<p><strong>Date:</strong> {{ $event->date ?? '' }}</p>
<a href="{{ route('events.index') }}">Back to Events</a>
@endsection
EOV

mkdir -p resources/views/admin/events
cat > resources/views/admin/events/create.blade.php << 'EOCE'
@extends('layouts.admin_theme')

@section('content')
<h1>Create New Event</h1>

<form method="POST" action="{{ route('admin.events.store') }}">
@csrf
<label>Title:</label>
<input type="text" name="title" required>

<label>Description:</label>
<textarea name="description"></textarea>

<label>Date:</label>
<input type="date" name="date" required>

<button type="submit">Create Event</button>
</form>

<a href="{{ route('admin.events.index') }}">Back to Events</a>
@endsection
EOCE

cat > resources/views/admin/events/edit.blade.php << 'EOED'
@extends('layouts.admin_theme')

@section('content')
<h1>Edit Event</h1>

<form method="POST" action="{{ route('admin.events.update', $id) }}">
@csrf
@method('PUT')

<label>Title:</label>
<input type="text" name="title" value="{{ old('title', '') }}" required>

<label>Description:</label>
<textarea name="description">{{ old('description', '') }}</textarea>

<label>Date:</label>
<input type="date" name="date" value="{{ old('date', '') }}" required>

<button type="submit">Update Event</button>
</form>

<a href="{{ route('admin.events.index') }}">Back to Events</a>
@endsection
EOED

mkdir -p resources/views/volunteer/certificates
cat > resources/views/volunteer/certificates/index.blade.php << 'EVCI'
@extends('layouts.app')

@section('content')
<h1>My Certificates</h1>

@if(count($certificates) > 0)
<ul>
@foreach($certificates as $certificate)
<li>
    <a href="{{ route('certificates.show', $certificate->id) }}">{{ $certificate->title }}</a>
</li>
@endforeach
</ul>
@else
<p>No certificates found.</p>
@endif

<a href="{{ route('volunteer.dashboard') }}">Back to Dashboard</a>
@endsection
EVCI

mkdir -p resources/views/profile
cat > resources/views/profile/show.blade.php << 'EPS'
@extends('layouts.app')

@section('content')
<h1>My Profile</h1>
<p>Name: {{ $user->name ?? '' }}</p>
<p>Email: {{ $user->email ?? '' }}</p>

<a href="{{ route('volunteer.profile.edit') }}">Edit Profile</a>
@endsection
EPS

# ====== Add backend controller methods ======

cat > app/Http/Controllers/EventRegistrationController.php << 'ECR'
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    public function register(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $user = Auth::user();

        // Check if already registered
        if(EventRegistration::where('event_id', $eventId)->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('info', 'You are already registered for this event.');
        }

        // Register user for event
        EventRegistration::create([
            'event_id' => $eventId,
            'user_id' => $user->id,
        ]);

        return redirect()->route('volunteer.dashboard')->with('success', 'Successfully registered for event.');
    }
}
ECR

cat > app/Http/Controllers/VolunteerHoursController.php << 'EVH'
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VolunteerHoursController extends Controller
{
    public function getHours()
    {
        $user = Auth::user();
        // TODO: Fetch total volunteer hours from DB
        $hours = 42; // Placeholder

        return view('volunteer.hours', compact('hours'));
    }
}
EVH

# ====== Add basic search/filter UI enhancements ======

cat > resources/views/opportunities/index.blade.php << 'EOI'
@extends('layouts.app')

@section('content')
<h1>Opportunities</h1>

<form method="GET" action="{{ route('opportunities.index') }}">
    <input type="text" name="search" placeholder="{{ __('messages.search_placeholder') }}" value="{{ request('search') }}">
    <button type="submit">{{ __('messages.search') }}</button>
</form>

@if($opportunities->count() > 0)
<ul>
@foreach($opportunities as $opportunity)
<li>{{ $opportunity->title }}</li>
@endforeach
</ul>

{{ $opportunities->links() }}
@else
<p>No opportunities found.</p>
@endif
@endsection
EOI

# ====== Add missing translations ======

cat > resources/lang/en/messages.php << 'EEN'
<?php

return [
    'site_title' => 'Swaed UAE',
    'home' => 'Home',
    'welcome_heading' => 'Welcome to Swaed UAE',
    'welcome_text' => 'Connecting volunteers with the best opportunities and events in the UAE.',
    'search_placeholder' => 'Search for opportunities, events, or sites...',
    'search' => 'Search',
    'registered_volunteers' => 'Registered Volunteers',
    'events_organized' => 'Events Organized',
    'volunteer_hours' => 'Volunteer Hours',
    'become_volunteer' => 'Become a Volunteer Now',
    'no_events_found' => 'No events found.',
    'event_registration_success' => 'Successfully registered for event.',
    'already_registered' => 'You are already registered for this event.',
];
EEN

cat > resources/lang/ar/messages.php << 'EAR'
<?php

return [
    'site_title' => 'ساعد الإمارات',
    'home' => 'الرئيسية',
    'welcome_heading' => 'مرحبا بكم في ساعد الإمارات',
    'welcome_text' => 'ربط المتطوعين بأفضل الفرص والفعاليات في الإمارات.',
    'search_placeholder' => 'ابحث عن فرص أو فعاليات أو موقع...',
    'search' => 'ابحث',
    'registered_volunteers' => 'المتطوعون المسجلون',
    'events_organized' => 'الفعاليات المنظمة',
    'volunteer_hours' => 'ساعات التطوع',
    'become_volunteer' => 'كن متطوعاً الآن',
    'no_events_found' => 'لم يتم العثور على فعاليات.',
    'event_registration_success' => 'تم التسجيل بنجاح في الفعالية.',
    'already_registered' => 'أنت مسجل بالفعل في هذه الفعالية.',
];
EAR

# ====== UI/UX polish (partial example) ======

cat > resources/views/layouts/navigation.blade.php << 'EON'
<nav class="bg-white shadow p-4 flex justify-between items-center">
    <a href="{{ route('home') }}" class="font-bold text-xl text-blue-600">Swaed UAE</a>
    <div>
        <a href="{{ route('home') }}" class="mr-4 {{ request()->routeIs('home') ? 'underline' : '' }}">{{ __('messages.home') }}</a>
        <a href="{{ route('opportunities.index') }}" class="mr-4 {{ request()->routeIs('opportunities.index') ? 'underline' : '' }}">Opportunities</a>
        <a href="{{ route('events.index') }}" class="mr-4 {{ request()->routeIs('events.index') ? 'underline' : '' }}">Events</a>
        <a href="{{ route('volunteer.dashboard') }}" class="mr-4 {{ request()->routeIs('volunteer.dashboard') ? 'underline' : '' }}">Dashboard</a>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-red-600">Logout</a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</nav>
EON

echo "Done adding missing features."
