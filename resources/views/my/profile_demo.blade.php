@extends(public.layout)
@section('title','Volunteer Profile (Demo)')
@section('content')
<link rel="stylesheet" href="/css/profile.css?v={{ filemtime(public_path('css/profile.css')) }}">
<section class="section"><div class="container">
  <div class="profile-wrap">
    <div class="p-card">
      <div class="p-hero d-flex align-items-center gap-3">
        <img class="photo" src="/images/avatar-default.svg" alt=""><div>
        <div class="fw-bold h5 mb-1">Geng Wang</div>
        <div class="small">Member since: 2019-04-26</div>
        <div class="small">Status: member</div></div>
      </div>
      <div class="p-pad">
        <div class="meta-grid mb-2">
          <div class="small-muted">Email:</div><div><a href="#">geng+demo@civ…</a></div>
          <div class="small-muted">Mobile:</div><div>(412) 296-1385</div>
          <div class="small-muted">Address:</div><div>1837 S Knightridge Rd, Bloomington, IN</div>
          <div class="small-muted">Age:</div><div>13+</div>
        </div>
        <div class="small-muted fw-bold mb-1">Emergency Contact:</div>
        <div class="meta-grid">
          <div class="small-muted">Name:</div><div>Stephanie Wang</div>
          <div class="small-muted">Relation:</div><div>Spouse</div>
          <div class="small-muted">Mobile:</div><div>(555) 555-5555</div>
        </div>
      </div>
    </div>
    <div class="d-grid gap-3">
      <div class="p-row">
        <div class="p-kpi"><div class="small-muted">Activities</div><div class="v">12</div></div>
        <div class="p-kpi"><div class="small-muted">Hours</div><div class="v">28.14</div></div>
        <div class="p-kpi"><div class="small-muted">Donations</div><div class="v">$0</div></div>
        <div class="p-kpi"><div class="small-muted">Last Activity</div><div class="v">01/23/2023</div></div>
        <div class="p-kpi"><div class="small-muted">Estimated Impact</div><div class="v">$842.76</div></div>
      </div>
      <div class="p-card">
        <div class="p-sec-h">Overview</div>
        <div class="p-pad list-slim">
          <div>1/23/2023 — “had a great time today…” <span class="small-muted">Event: Ezekiel Shelter</span></div>
          <div>1/20/2023 — “I had a great time” <span class="small-muted">Event: Ezekiel Shelter</span></div>
          <div>…</div>
        </div>
      </div>
      <div class="p-card">
        <div class="p-sec-h">Signed Waivers</div>
        <div class="p-pad"><div class="d-flex justify-content-between">
          <div>Allergy Disclosure</div><div><span class="badge-ok">Valid</span> <span class="small-muted ms-2">09/15/2023</span></div>
        </div></div>
      </div>
    </div>
  </div>
</div></section>
@endsection
