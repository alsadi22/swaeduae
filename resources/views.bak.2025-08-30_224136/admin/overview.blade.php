@extends('admin.layout')
@section('title', __('Admin Overview'))
@section('content')
<div class="container py-4">
  <h1 class="mb-4">{{ __('Admin Overview') }}</h1>

  <div class="row g-3">
    <div class="col-md-3"><div class="card p-3 h-100"><div class="text-muted">{{ __('Users') }}</div><div class="h3">{{ $kpi['users'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><div class="text-muted">{{ __('Opportunities') }}</div><div class="h3">{{ $kpi['opportunities'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><div class="text-muted">{{ __('Applications') }}</div><div class="h3">{{ $kpi['applications'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><div class="text-muted">{{ __('Minutes Logged') }}</div><div class="h3">{{ $kpi['hours_minutes'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><div class="text-muted">{{ __('Certificates') }}</div><div class="h3">{{ $kpi['certificates'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><div class="text-muted">{{ __('New Partners') }}</div><div class="h3">{{ $kpi['partners_new'] }}</div></div></div>
    <div class="col-md-3"><div class="card p-3 h-100"><div class="text-muted">{{ __('Audit (today)') }}</div><div class="h3">{{ $kpi['audit_today'] }}</div></div></div>
  </div>

  <div class="mt-4 d-flex gap-2 flex-wrap">
    <a class="btn btn-outline-primary" href="{{ route('admin.export.users') }}">{{ __('Export Users CSV') }}</a>
    <a class="btn btn-outline-primary" href="{{ route('admin.export.hours') }}">{{ __('Export Hours CSV') }}</a>
    <a class="btn btn-outline-primary" href="{{ route('admin.export.certificates') }}">{{ __('Export Certificates CSV') }}</a>
    <a class="btn btn-outline-primary" href="{{ route('admin.export.applications') }}">{{ __('Export Applications CSV') }}</a>
  </div>

  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card p-3">
        <h5 class="mb-3">{{ __('Latest Applications') }}</h5>
        @forelse($latestApps as $a)
          <div class="d-flex justify-content-between border-bottom py-1">
            <div>{{ $a->opportunity_title ?? ('#'.$a->opportunity_id) }}</div>
            <div class="text-muted small">{{ $a->created_at }}</div>
          </div>
        @empty
          <div class="text-muted">{{ __('No applications yet.') }}</div>
        @endforelse
      </div>
    </div>
    <div class="col-md-6">
      <div class="card p-3">
        <h5 class="mb-3">{{ __('Latest Certificates') }}</h5>
        @forelse($latestCerts as $c)
          <div class="d-flex justify-content-between border-bottom py-1">
            <div>{{ $c->code }}</div>
            <div class="text-muted small">{{ $c->created_at }}</div>
          </div>
        @empty
          <div class="text-muted">{{ __('No certificates yet.') }}</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
