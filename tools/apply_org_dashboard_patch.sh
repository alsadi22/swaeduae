#!/usr/bin/env bash
PHP_BIN=${PHP_BIN:-php}
set -euo pipefail

ts() { date +%F_%H%M%S; }
backup() { [ -f "$1" ] && cp "$1" "$1.bak_$(ts)" || true; }

install -d resources/views/org/{dashboard,partials} tools

LAY=resources/views/org/layout.blade.php
backup "$LAY"
cat > "$LAY" <<'BLADE'
@php $rtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Organization Console')</title>

  <!-- Argon Dashboard CSS -->
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-svg.css') }}">
  <link id="pagestyle" rel="stylesheet" href="{{ asset('vendor/argon/assets/css/argon-dashboard.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/brand.css') }}">

  @stack('head')
  @includeIf('org.partials.branding_styles')
  <style>
    .card { border-radius: 14px; }
    .btn-chip { border-radius: 10px; padding: .5rem .9rem; }
    .kpi .icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; }
    .list-clean { list-style: none; padding-left: 0; margin: 0; }
    .list-clean li { display:flex; align-items:center; justify-content:space-between; padding:.4rem 0; border-bottom: 1px solid #f1f2f6; }
    .list-clean li:last-child { border-bottom: 0; }
  </style>
</head>
<body class="g-sidenav-show bg-gray-100 {{ $rtl ? 'rtl' : '' }}">
  @includeIf('org.argon._sidenav')

  <main class="main-content position-relative border-radius-lg {{ $rtl ? 'me-3' : 'ms-3' }}">
    @includeIf('admin.argon._navbar')

    <div class="container-fluid py-4">
      @if (session('status'))
        <div class="alert alert-success shadow-sm">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
          <ul class="m-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      @includeIf('org.partials.menu')

      @yield('content')

      @includeIf('admin.argon._footer')
    </div>
  </main>

  <script src="{{ asset('vendor/argon/assets/js/core/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/argon/assets/js/argon-dashboard.min.js') }}"></script>
  <script>(function(){if(!window.Chart){var s=document.createElement('script');s.src='https://cdn.jsdelivr.net/npm/chart.js';s.defer=true;document.head.appendChild(s);}})();</script>
  @stack('scripts')
</body>
</html>
BLADE

# fix any broken include syntax in layout (defensive)
sed -Ei "s/@include\(\s*(['\"][^'\"]+['\"])\\s*,\\s*\)/@include(\1)/g" "$LAY"
sed -Ei \
  -e "s/^[[:space:]]*\('org\.argon\._sidenav'\)[[:space:]]*$/@includeIf('org.argon._sidenav')/g" \
  -e "s/^[[:space:]]*\('admin\.argon\._navbar'\)[[:space:]]*$/@includeIf('admin.argon._navbar')/g" \
  -e "s/^[[:space:]]*\('org\.partials\.menu'\)[[:space:]]*$/@includeIf('org.partials.menu')/g" \
  -e "s/^[[:space:]]*\('admin\.argon\._footer'\)[[:space:]]*$/@includeIf('admin.argon._footer')/g" \
  "$LAY" || true

# safe defaults
SAFE=resources/views/org/dashboard/_safe_defaults.blade.php
backup "$SAFE"
cat > "$SAFE" <<'BLADE'
@php
  $volunteersHosted   = (int)($volunteersHosted   ?? 0);
  $totalHours         = (float)($totalHours       ?? 0);
  $upcomingOpps       = (int)($upcomingOpps       ?? 0);
  $certificatesIssued = (int)($certificatesIssued ?? 0);

  $appsPending   = (int)($appsPending   ?? 0);
  $appsApproved  = (int)($appsApproved  ?? 0);
  $appsTotal     = (int)($appsTotal     ?? 0);
  $checkinsToday = (int)($checkinsToday ?? 0);

  $monthLabels = is_array($monthLabels ?? null) ? $monthLabels : [];
  $hoursSeries = is_array($hoursSeries ?? null) ? $hoursSeries : [];
  $appAttend   = is_array($appAttend   ?? null)
                ? array_merge(['labels'=>[], 'apps'=>[], 'attend'=>[]], $appAttend)
                : ['labels'=>[], 'apps'=>[], 'attend'=>[]];

  $recentActivity = collect($recentActivity ?? []);
  $upcoming       = collect($upcoming ?? []);
  $rows           = collect($rows ?? []);
@endphp
BLADE

# KPIs
KPIS=resources/views/org/dashboard/_kpis.blade.php
backup "$KPIS"
cat > "$KPIS" <<'BLADE'
<div class="row g-3">
  <div class="col-12 col-md-3">
    <div class="card kpi shadow-sm">
      <div class="card-body d-flex gap-3 align-items-center">
        <div class="icon bg-primary-subtle"><i class="ni ni-badge text-primary"></i></div>
        <div><div class="text-muted small">{{ __('CERTIFICATES ISSUED') }}</div><div class="h4 m-0">{{ $certificatesIssued }}</div></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card kpi shadow-sm">
      <div class="card-body d-flex gap-3 align-items-center">
        <div class="icon bg-info-subtle"><i class="ni ni-calendar-grid-58 text-info"></i></div>
        <div><div class="text-muted small">{{ __('UPCOMING OPPORTUNITIES') }}</div><div class="h4 m-0">{{ $upcomingOpps }}</div></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card kpi shadow-sm">
      <div class="card-body d-flex gap-3 align-items-center">
        <div class="icon bg-primary-subtle"><i class="ni ni-time-alarm text-primary"></i></div>
        <div><div class="text-muted small">{{ __('TOTAL HOURS CONTRIBUTED') }}</div><div class="h4 m-0">hrs {{ number_format($totalHours, 2) }}</div></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card kpi shadow-sm">
      <div class="card-body d-flex gap-3 align-items-center">
        <div class="icon bg-info-subtle"><i class="ni ni-single-02 text-info"></i></div>
        <div><div class="text-muted small">{{ __('TOTAL VOLUNTEERS') }}</div><div class="h4 m-0">{{ $volunteersHosted }}</div></div>
      </div>
    </div>
  </div>
</div>
BLADE

# top counters + chips
DASH1=resources/views/org/partials/dashboard_v1.blade.php
backup "$DASH1"
cat > "$DASH1" <<'BLADE'
@include('org.dashboard._safe_defaults')
@php $pending=$appsPending; $approved=$appsApproved; $applicants=$appsTotal; $today=$checkinsToday; @endphp
<div class="row g-3">
  <div class="col-6 col-md-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted small mb-1">{{ __('Pending') }}</div><div class="h3 m-0">{{ $pending }}</div></div></div></div>
  <div class="col-6 col-md-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted small mb-1">{{ __('Approved') }}</div><div class="h3 m-0">{{ $approved }}</div></div></div></div>
  <div class="col-6 col-md-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted small mb-1">{{ __('Applicants') }}</div><div class="h3 m-0">{{ $applicants }}</div></div></div></div>
  <div class="col-6 col-md-3"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted small mb-1">{{ __('Today Check-ins') }}</div><div class="h3 m-0">{{ $today }}</div></div></div></div>
</div>
<div class="mt-3 d-flex gap-2 flex-wrap">
  <a class="btn btn-outline-secondary btn-chip" href="{{ url('/org/email-preview') }}">{{ __('Email Preview') }}</a>
  <a class="btn btn-outline-secondary btn-chip" href="{{ route('org.settings.edit') }}">{{ __('Settings') }}</a>
  <a class="btn btn-outline-secondary btn-chip" href="{{ url('/org/kyc') }}">{{ __('KYC / License') }}</a>
  <a class="btn btn-outline-secondary btn-chip" href="{{ url('/org/team') }}">{{ __('Team') }}</a>
  <a class="btn btn-outline-secondary btn-chip" href="{{ url('/org/shortlist') }}">{{ __('Shortlist') }}</a>
  <a class="btn btn-outline-secondary btn-chip" href="{{ url('/org/applicants') }}">{{ __('Applicants') }}</a>
  <a class="btn btn-primary btn-chip" href="#">{{ __('Dashboard') }}</a>
  <div class="ms-auto d-flex gap-2">
    <a class="btn btn-light btn-chip" href="?range=30d">{{ __('Last 30d') }}</a>
    <a class="btn btn-light btn-chip" href="?range=7d">{{ __('Last 7d') }}</a>
    <a class="btn btn-primary btn-chip" href="?apply=1">{{ __('Apply') }}</a>
  </div>
</div>
BLADE

# bar chart (apps vs attendance)
APPA=resources/views/org/partials/apps_vs_attend.blade.php
backup "$APPA"
cat > "$APPA" <<'BLADE'
@include('org.dashboard._safe_defaults')
<div class="card shadow-sm h-100"><div class="card-body"><h6 class="mb-3">{{ __('Applications vs Attendance') }}</h6><canvas id="appsVsAttend" height="120"></canvas></div></div>
@push('scripts')
<script>
(function renderAppsVsAttend(){
  function draw(){
    if(!window.Chart) return setTimeout(draw,120);
    const ctx=document.getElementById('appsVsAttend'); if(!ctx) return;
    const labels=@json($appAttend['labels']); const apps=@json($appAttend['apps']); const attend=@json($appAttend['attend']);
    new Chart(ctx,{type:'bar',data:{labels,datasets:[{label:'Applications',data:apps},{label:'Attendance',data:attend}]},
      options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'top'}}}});
  } draw();
})();
</script>
@endpush
BLADE

# line chart (hours)
HOURS=resources/views/org/partials/hours_chart.blade.php
backup "$HOURS"
cat > "$HOURS" <<'BLADE'
@include('org.dashboard._safe_defaults')
<div class="card shadow-sm h-100"><div class="card-body"><h6 class="mb-3">{{ __('Volunteer Hours') }}</h6><canvas id="hoursLine" height="120"></canvas></div></div>
@push('scripts')
<script>
(function renderHours(){
  function draw(){
    if(!window.Chart) return setTimeout(draw,120);
    const ctx=document.getElementById('hoursLine'); if(!ctx) return;
    const labels=@json($monthLabels); const data=@json($hoursSeries);
    new Chart(ctx,{type:'line',data:{labels,datasets:[{label:'Hours',data,fill:true,tension:.35}]},
      options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
  } draw();
})();
</script>
@endpush
BLADE

# recent activity
REC=resources/views/org/partials/recent_activity.blade.php
backup "$REC"
cat > "$REC" <<'BLADE'
@include('org.dashboard._safe_defaults')
<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Recent activity') }}</h6>
    <ul class="list-clean">
      @forelse ($recentActivity as $r)
        <li>
          <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-light text-dark">{{ \Illuminate\Support\Str::substr($r['who'] ?? '—',0,3) }}</span>
            <strong>{{ $r['who'] ?? '—' }}</strong>
          </div>
          <span class="badge bg-secondary-subtle text-dark">{{ ($r['type'] ?? '') === 'attendance' ? __('Attendance') : __('Application') }}</span>
        </li>
      @empty
        <li class="text-muted">{{ __('No recent activity') }}</li>
      @endforelse
    </ul>
  </div>
</div>
BLADE

# upcoming 7d
UP7=resources/views/org/partials/upcoming_7d.blade.php
backup "$UP7"
cat > "$UP7" <<'BLADE'
@include('org.dashboard._safe_defaults')
@php $list = $upcoming; @endphp
<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Upcoming (7 days)') }}</h6>
    <ul class="list-clean">
      @forelse ($list as $it)
        <li>
          <div class="d-flex gap-2 align-items-center"><span class="badge bg-primary-subtle">•</span><span>{{ $it['title'] ?? ($it->title ?? '—') }}</span></div>
          <span class="text-muted small">{{ $it['day'] ?? ($it->day ?? '') }}</span>
        </li>
      @empty
        <li class="text-muted">{{ __('No upcoming items') }}</li>
      @endforelse
    </ul>
  </div>
</div>
BLADE

# today check-ins
TCI=resources/views/org/partials/today_checkins.blade.php
backup "$TCI"
cat > "$TCI" <<'BLADE'
@include('org.dashboard._safe_defaults')
@php $rows = $rows; @endphp
<div class="card shadow-sm h-100">
  <div class="card-body">
    <h6 class="mb-3">{{ __('Today check-ins') }}</h6>
    <ul class="list-clean">
      @forelse ($rows as $r)
        <li>
          <div class="d-flex gap-2 align-items-center"><span class="badge bg-primary-subtle">•</span><span>{{ $r['name'] ?? ($r->name ?? '—') }}</span></div>
          <span class="text-muted small">{{ $r['time'] ?? ($r->time ?? '') }}</span>
        </li>
      @empty
        <li class="text-muted">{{ __('No check-ins yet') }}</li>
      @endforelse
    </ul>
  </div>
</div>
BLADE

# dashboard composer
MAIN=resources/views/org/dashboard.blade.php
backup "$MAIN"
cat > "$MAIN" <<'BLADE'
@extends('org.layout')
@include('org.dashboard._safe_defaults')

@section('title','Organization Dashboard')

@section('content')
  @include('org.partials.dashboard_v1')

  <div class="mt-3">
    @include('org.dashboard._kpis')
  </div>

  <div class="row g-3 mt-1">
    <div class="col-12 col-lg-6">@include('org.partials.apps_vs_attend')</div>
    <div class="col-12 col-lg-6">@include('org.partials.hours_chart')</div>
  </div>

  <div class="row g-3 mt-1">
    <div class="col-12 col-lg-4">@include('org.partials.recent_activity')</div>
    <div class="col-12 col-lg-4">@include('org.partials.upcoming_7d')</div>
    <div class="col-12 col-lg-4">@include('org.partials.today_checkins')</div>
  </div>
@endsection
BLADE

echo "Clearing & caching views…"
php artisan view:clear >/dev/null
php artisan view:cache >/dev/null
echo "Done. Visit /org/dashboard"
