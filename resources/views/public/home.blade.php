@extends('public.layout')
@section('title','SwaedUAE')
@section('content')
<section class="hero">
  <div class="container">
    <div>
      <span class="badge">Volunteer Platform · UAE</span>
      <h1>سواعد الإمارات</h1>
      <p class="lead">Connecting volunteers with opportunities across the UAE.</p>
      <div class="buttons">
        <a class="btn primary" href="/opportunities">Browse Opportunities</a>
        <a class="btn secondary" href="/events">Events</a>
      </div>
      <div class="stats">
        <div class="stat"><div class="n">1,200+</div><div class="muted">Volunteers</div></div>
        <div class="stat"><div class="n">85</div><div class="muted">Organizations</div></div>
        <div class="stat"><div class="n">320+</div><div class="muted">Events</div></div>
      </div>
    </div>
    <div class="card">
      <h3>Why SwaedUAE?</h3>
      <p>QR-based check-in/out, verified hours, and downloadable certificates. Organizations can post roles and manage applicants with ease.</p>
      <hr class="sep">
      <div class="grid-2">
        <div><span class="kicker">Check-in</span><br>Scan a QR to start/stop your shift.</div>
        <div><span class="kicker">Track</span><br>Hours are tallied and verified automatically.</div>
        <div><span class="kicker">Recognize</span><br>Issue secure certificates in one click.</div>
        <div><span class="kicker">Grow</span><br>Find more ways to serve your community.</div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cards">
      <div class="card"><h3>Fast check-in</h3><p class="muted">QR codes + geofence to keep things accurate.</p></div>
      <div class="card"><h3>Verified hours</h3><p class="muted">Automatic hour tracking for every activity.</p></div>
      <div class="card"><h3>Certificates</h3><p class="muted">Download and share verifiable certificates.</p></div>
      <div class="card"><h3>Org dashboard</h3><p class="muted">Post opportunities and manage applicants.</p></div>
    </div>
  </div>
</section>
@endsection
