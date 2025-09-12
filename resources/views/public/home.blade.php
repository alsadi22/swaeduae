@extends("public.layout")
@section("title","SwaedUAE — Volunteering Platform")
@section("content")
<section class="hero">
  <div class="container">
    <div class="eyebrow">Volunteer Platform · UAE</div>
    <h1>سواعدُ الإمارات</h1>
    <p class="sub">Connecting volunteers with opportunities across the UAE — verified hours, QR check-in/out, and downloadable certificates.</p>
    <div class="cta">
      <a class="btn" href="/opportunities">Browse Opportunities</a>
      <a class="btn outline" href="/about">Learn more</a>
    </div>

    <div class="stats">
      <div><div class="num">+320</div><div class="lbl">Events</div></div>
      <div><div class="num">85</div><div class="lbl">Organizations</div></div>
      <div><div class="num">+1,200</div><div class="lbl">Volunteers</div></div>
    </div>
  </div>
</section>

<section class="cards container">
  <article class="card"><div class="ic">📈</div><h3>Grow & serve</h3><p>Discover new ways to serve your community with trusted partners.</p></article>
  <article class="card"><div class="ic">🎓</div><h3>Certificates</h3><p>Issue secure certificates in one click — share or download anytime.</p></article>
  <article class="card"><div class="ic">✅</div><h3>Verified hours</h3><p>Hours are tallied and verified automatically for each activity.</p></article>
  <article class="card"><div class="ic">📱</div><h3>Fast check-in</h3><p>Scan a QR to start/stop your shift — with geofence to keep things accurate.</p></article>
</section>

<section class="cta-band">
  <div class="container">
    <div>
      <h3>Ready to help?</h3>
      <p>Create your account and join upcoming activities today.</p>
    </div>
    <div>
      <a class="btn" href="/opportunities">Browse Opportunities</a>
      <a class="btn outline" href="/contact">Contact us</a>
    </div>
  </div>
</section>
@endsection
