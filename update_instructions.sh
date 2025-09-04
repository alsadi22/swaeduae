#!/bin/bash
# This script appends missing route definitions and creates minimal controllers
# and views needed to resolve common RouteNotFoundException errors in a
# Laravel application. It assumes you are running it from the root of the
# project (where the routes, app, and resources directories exist).

set -e

echo "Adding static page routes…"
# Static pages: about, contact, faq, team, partners, home
cat <<'EOS' >> routes/web.php

// --- Added static page routes ---
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/faq', 'faq')->name('faq');
Route::view('/team', 'team')->name('team');
Route::view('/partners', 'partners')->name('partners');
Route::view('/home', 'welcome')->name('home');
EOS

echo "Adding dashboard and profile routes…"
cat <<'EOS' >> routes/web.php

// --- Added authenticated user routes ---
Route::middleware(['auth'])->group(function() {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/profile', 'profile')->name('profile');
});
EOS

echo "Adding Opportunities routes…"
cat <<'EOS' >> routes/web.php

// --- Added volunteer opportunities routes ---
use App\Http\Controllers\OpportunityController;

Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities.index');
Route::get('/opportunities/{id}', [OpportunityController::class, 'show'])->name('opportunities.show');
EOS

echo "Adding Events routes…"
cat <<'EOS' >> routes/web.php

// --- Added events routes ---
use App\Http\Controllers\EventController;

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');
EOS

echo "Adding News routes…"
cat <<'EOS' >> routes/web.php

// --- Added news routes ---
use App\Http\Controllers\NewsController;

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');
EOS

echo "Adding language switcher and logout routes…"
cat <<'EOS' >> routes/web.php

// --- Added language switcher route ---
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// --- Added logout route ---
use Illuminate\Support\Facades\Auth;

Route::post('/logout', function() {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');
EOS

echo "Creating controllers if they do not exist…"
mkdir -p app/Http/Controllers

if [ ! -f app/Http/Controllers/OpportunityController.php ]; then
cat <<'EOS' > app/Http/Controllers/OpportunityController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index()
    {
        return view('opportunities.index');
    }

    public function show($id)
    {
        return view('opportunities.show', compact('id'));
    }
}
EOS
fi

if [ ! -f app/Http/Controllers/EventController.php ]; then
cat <<'EOS' > app/Http/Controllers/EventController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return view('events.index');
    }

    public function show($id)
    {
        return view('events.show', compact('id'));
    }
}
EOS
fi

if [ ! -f app/Http/Controllers/NewsController.php ]; then
cat <<'EOS' > app/Http/Controllers/NewsController.php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        return view('news.index');
    }

    public function show($id)
    {
        return view('news.show', compact('id'));
    }
}
EOS
fi

echo "Creating minimal Blade views if missing…"
mkdir -p resources/views/opportunities
mkdir -p resources/views/events
mkdir -p resources/views/news

# Static pages
for page in about contact faq team partners; do
    if [ ! -f "resources/views/$page.blade.php" ]; then
cat <<EOS > "resources/views/$page.blade.php"
@extends('layouts.app')

@section('content')
    <h1>$(echo $page | tr '[:lower:]' '[:upper:]')</h1>
    <p>Content for the $page page goes here.</p>
@endsection
EOS
    fi
done

# Dashboard & Profile
if [ ! -f resources/views/dashboard.blade.php ]; then
cat <<'EOS' > resources/views/dashboard.blade.php
@extends('layouts.app')

@section('content')
    <h1>User Dashboard</h1>
    <p>Welcome to your dashboard.</p>
@endsection
EOS
fi

if [ ! -f resources/views/profile.blade.php ]; then
cat <<'EOS' > resources/views/profile.blade.php
@extends('layouts.app')

@section('content')
    <h1>User Profile</h1>
    <p>Edit your profile details here.</p>
@endsection
EOS
fi

# Opportunities views
if [ ! -f resources/views/opportunities/index.blade.php ]; then
cat <<'EOS' > resources/views/opportunities/index.blade.php
@extends('layouts.app')

@section('content')
    <h1>Volunteer Opportunities</h1>
    <p>No opportunities found.</p>
@endsection
EOS
fi

if [ ! -f resources/views/opportunities/show.blade.php ]; then
cat <<'EOS' > resources/views/opportunities/show.blade.php
@extends('layouts.app')

@section('content')
    <h1>Opportunity Details</h1>
    <p>Details for opportunity ID: {{ $id }}</p>
@endsection
EOS
fi

# Events views
if [ ! -f resources/views/events/index.blade.php ]; then
cat <<'EOS' > resources/views/events/index.blade.php
@extends('layouts.app')

@section('content')
    <h1>Events</h1>
    <p>No events found.</p>
@endsection
EOS
fi

if [ ! -f resources/views/events/show.blade.php ]; then
cat <<'EOS' > resources/views/events/show.blade.php
@extends('layouts.app')

@section('content')
    <h1>Event Details</h1>
    <p>Details for event ID: {{ $id }}</p>
@endsection
EOS
fi

# News views
if [ ! -f resources/views/news/index.blade.php ]; then
cat <<'EOS' > resources/views/news/index.blade.php
@extends('layouts.app')

@section('content')
    <h1>News</h1>
    <p>No news items found.</p>
@endsection
EOS
fi

if [ ! -f resources/views/news/show.blade.php ]; then
cat <<'EOS' > resources/views/news/show.blade.php
@extends('layouts.app')

@section('content')
    <h1>News Item</h1>
    <p>Details for news ID: {{ $id }}</p>
@endsection
EOS
fi

echo "Update complete."