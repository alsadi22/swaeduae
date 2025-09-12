@extends(public.layout)
@section('title','Volunteer Profile')
@section('content')
<link rel="stylesheet" href="/css/profile.css?v={{ filemtime(public_path('css/profile.css')) }}">
<section class="section"><div class="container">
  <div class="profile-wrap">

    {{-- LEFT: identity --}}
    <div class="p-card">
      <div class="p-hero d-flex align-items-center gap-3">
        <img class="photo" src="{{ $profile['photo'] ?? '/images/avatar-default.svg' }}" alt="">
        <div>
          <div class="fw-bold h5 mb-1">{{ $profile['name'] ?? 'Volunteer' }}</div>
          <div class="small">Member since: {{ $profile['member_since'] ?? '—' }}</div>
          <div class="small">Status: {{ $profile['status'] ?? 'member' }}</div>
        </div>
      </div>
      <div class="p-pad">
        <div class="meta-grid mb-2">
          <div class="small-muted">Email:</div><div><a href="mailto:{{ $profile['email'] ?? '' }}">{{ $profile['email'] ?? '—' }}</a></div>
          <div class="small-muted">Mobile:</div><div>{{ $profile['mobile'] ?? '—' }}</div>
          <div class="small-muted">Address:</div><div>{{ $profile['address'] ?? '—' }}</div>
          <div class="small-muted">Age:</div><div>{{ $profile['age'] ?? '—' }}</div>
        </div>
        <div class="small-muted fw-bold mb-1">Emergency Contact:</div>
        <div class="meta-grid">
          <div class="small-muted">Name:</div><div>{{ data_get($profile,'emergency.name','—') }}</div>
          <div class="small-muted">Relation:</div><div>{{ data_get($profile,'emergency.relationship','—') }}</div>
          <div class="small-muted">Mobile:</div><div>{{ data_get($profile,'emergency.mobile','—') }}</div>
        </div>
      </div>
    </div>

    {{-- RIGHT: metrics + sections --}}
    <div class="d-grid gap-3">
      <div class="p-row">
        <div class="p-kpi"><div class="small-muted">Activities</div><div class="v">{{ $metrics['activities'] }}</div></div>
        <div class="p-kpi"><div class="small-muted">Hours</div><div class="v">{{ $metrics['hours'] }}</div></div>
        <div class="p-kpi"><div class="small-muted">Donations</div><div class="v">{{ $metrics['donations'] }}</div></div>
        <div class="p-kpi"><div class="small-muted">Last Activity</div><div class="v">{{ $metrics['last_activity'] ?: '—' }}</div></div>
        <div class="p-kpi"><div class="small-muted">Estimated Impact</div><div class="v">{{ $metrics['impact'] }}</div></div>
      </div>

      <div class="p-card">
        <div class="p-sec-h">Overview</div>
        <div class="p-pad list-slim">
          <div class="small-muted mb-1">Recent reflections:</div>
          @forelse($recent as $r)
            <div class="mb-1">{{ $r['date'] }} — “{{ $r['note'] }}” <span class="small-muted"> Event: {{ $r['event'] }}</span></div>
          @empty
            <div class="small-muted">No notes yet.</div>
          @endforelse
        </div>
      </div>

      <div class="p-card">
        <div class="p-sec-h">Signed Waivers</div>
        <div class="p-pad list-slim">
          @forelse($waivers as $w)
            <div class="d-flex justify-content-between">
              <div>{{ $w['name'] }}</div>
              <div><span class="badge-ok">{{ $w['status'] }}</span> <span class="small-muted ms-2">{{ $w['expires'] }}</span></div>
            </div>
          @empty
            <div class="small-muted">No waivers on file.</div>
          @endforelse
        </div>
      </div>
    </div>

  </div>
</div></section>
@endsection
