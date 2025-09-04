@extends('org.layout')
@include('org.dashboard._safe_defaults')

@section('title','Organization Dashboard')

@section('content')
@include('org/dashboard/_kpis')
@include('org/partials/apps_vs_attend')
@include('org/partials/hours_chart')
@include('org/partials/recent_activity')
@include('org/partials/upcoming_7d')
@include('org/partials/today_checkins')

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
