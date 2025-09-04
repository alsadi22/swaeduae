@extends("org.layout")
@php /*__UPCOMING_DISPLAY__*/ $upcoming_display = isset($upcomingOpps) ? (int)$upcomingOpps : (is_countable($upcoming ?? null) ? count($upcoming) : (int)($upcoming ?? 0)); @endphp
@php /*__ORG_GUARDS__*/ $appsTotal=$appsTotal??0; $appsPending=$appsPending??0; $appsApproved=$appsApproved??0; $checkinsToday=$checkinsToday??0; @endphp
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
