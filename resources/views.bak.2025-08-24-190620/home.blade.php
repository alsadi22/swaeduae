@extends('layouts.public')
@section('title', config('app.name').' — SawaedUAE')

@section('content')
@php
  // Always have a hero image
  $hero = $hero ?? ($cover ?? asset(app()->isLocale('ar') ? 'images/hero_ar.jpg' : 'images/hero.jpg'));
  // Normalize tiles into a collection if provided
  $tiles = isset($tiles) ? collect($tiles) : collect();
@endphp

<div class="container my-4 my-md-5">
  <div class="hero-card mb-4 mb-md-5" style="background-image:url('{{ $hero }}')">
    <div class="text-center" style="max-width: 840px;">
      <h1 class="fw-bold mb-3">{{ __('Find Volunteer Opportunities in the UAE') }}</h1>
      <p class="text-muted mb-4">{{ __('Join events, track your hours, and earn verified certificates') }}.</p>
      <div class="d-flex gap-2 justify-content-center">
        <a class="btn btn-outline-secondary" href="{{ url('/events') }}">{{ __('Browse Events') }}</a>
        <a class="btn btn-primary" href="{{ url('/opportunities') }}">{{ __('Explore Opportunities') }}</a>
      </div>
    </div>
  </div>
</div>

@if($tiles->isNotEmpty())
<div class="container my-4 my-md-5">
  <h2 class="h4 mb-3">{{ __('Latest') }}</h2>
  <div class="row g-3">
    @foreach($tiles as $t)
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="small text-muted mb-1">{{ ucfirst($t->type ?? 'item') }}</div>
            <div class="fw-semibold">{{ $t->title ?? '' }}</div>
            @if(!empty($t->date))
              <div class="text-muted small">
                {{ \Illuminate\Support\Carbon::parse($t->date)->toFormattedDateString() }}
              </div>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endif
@endsection
