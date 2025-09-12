@extends('layouts.app')

@section('content')
  @include('partials.hero-home')

<div class="container py-5">
    <div class="p-5 rounded-4 shadow-lg" style="background: linear-gradient(135deg,#FBF3D5 0%, #D6DAC8 50%, #9CAFAA 100%);">
        <h1 class="fw-bold mb-3">{{ __('Find Volunteer Opportunities in the UAE') }}</h1>
        <p class="lead mb-4">{{ __('Join events, track your hours, and earn verified certificates.') }}</p>
        <a href="{{ url('/opportunities') }}" class="btn btn-primary btn-lg me-2">{{ __('Explore Opportunities') }}</a>
        <a href="{{ url('/events') }}" class="btn btn-outline-secondary btn-lg">{{ __('Browse Events') }}</a>
    </div>
</div>
@endsection
