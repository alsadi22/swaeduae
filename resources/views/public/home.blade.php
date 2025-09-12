@extends('public.layout')
@section('title','SwaedUAE — Volunteer Platform')

@section('content')
<section class="hero">
  <div class="hero-body container">
    <p class="eyebrow">Volunteer Platform · UAE</p>
    <h1 class="hero-title">سواعدُ الإمارات</h1>
    <p class="hero-sub">Connecting volunteers with opportunities across the UAE — verified hours, QR check-in/out, and downloadable certificates.</p>

    <div class="actions">
      <a href="{{ url('/opportunities') }}" class="btn btn-primary">Browse Opportunities</a>
      <a href="{{ url('/events') }}" class="btn">Events</a>
    </div>

    <div class="stats">
      <div class="stat"><span>1,200+</span><small>Volunteers</small></div>
      <div class="stat"><span>85</span><small>Organizations</small></div>
      <div class="stat"><span>320+</span><small>Events</small></div>
    </div>
  </div>
</section>

<section class="features container">
  <div class="feature">
    <div class="f-icon">📱</div>
    <h3>Fast check-in</h3>
    <p>Scan a QR to start/stop your shift — with geofence to keep things accurate.</p>
  </div>
  <div class="feature">
    <div class="f-icon">✅</div>
    <h3>Verified hours</h3>
    <p>Hours are tallied and verified automatically for each activity.</p>
  </div>
  <div class="feature">
    <div class="f-icon">🎓</div>
    <h3>Certificates</h3>
    <p>Issue secure certificates in one click — share or download anytime.</p>
  </div>
  <div class="feature">
    <div class="f-icon">📈</div>
    <h3>Grow &amp; serve</h3>
    <p>Discover new ways to serve your community with trusted partners.</p>
  </div>
</section>

<section class="cta container">
  <div class="cta-card">
    <div>
      <h3>Ready to help?</h3>
      <p>Create your account and join upcoming activities today.</p>
    </div>
    <div class="cta-actions">
      <a class="btn btn-primary" href="{{ url('/register') }}">Get started</a>
      <a class="btn" href="{{ url('/login') }}">Sign in</a>
    </div>
  </div>
</section>
@endsection
