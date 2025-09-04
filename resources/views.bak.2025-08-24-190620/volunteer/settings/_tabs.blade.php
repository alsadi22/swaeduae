@php($certCount = $certCount ?? 0)
@php($hoursSum = $hoursSum ?? 0)
@php($upcoming = $upcoming ?? 0)
@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    // Prefer controller-provided $stats; otherwise compute safe fallbacks only if tables exist.
    $stats = $stats ?? [];
    $certs   = $stats['certificates'] ?? 0;
    $hours   = $stats['hours'] ?? 0;
    $upcoming= $stats['upcoming'] ?? 0;

    try {
        if (!isset($stats['certificates']) && \Illuminate\Support\Facades\Schema::hasTable('certificates')) {
        }
        if (!isset($stats['hours']) && \Illuminate\Support\Facades\Schema::hasTable('volunteer_hours')) {
        }
        if (!isset($stats['upcoming']) && \Illuminate\Support\Facades\Schema::hasTable('opportunity_applications') && \Illuminate\Support\Facades\Schema::hasTable('opportunities')) {
                ->join('opportunities','opportunity_applications.opportunity_id','=','opportunities.id')
                
                ->whereDate('opportunities.created_at','>=', now()->toDateString())
                ->count();
        }
    } catch (\Throwable $e) { /* stay silent; show zeroes if schema differs */ }
@endphp

<div class="container py-4">
  <div class="row mb-4">
    <div class="col">
      <h1 class="h3 mb-1">Volunteer Dashboard</h1>
      <div class="text-muted">Welcome back, {{ $user->name }}</div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">CERTIFICATES</div>
          <div class="display-6 fw-semibold">{{ number_format((float)$certs, 0) }}</div>
          <div class="mt-3">
            <a class="btn btn-sm btn-outline-primary"
               href="{{ \Illuminate\Support\Facades\Route::has('certificates.my') ? route('certificates.my') : url('/my/certificates') }}">
              View Certificates
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">UPCOMING OPPORTUNITIES</div>
          <div class="display-6 fw-semibold">{{ number_format((float)$upcoming, 0) }}</div>
          <div class="mt-3">
            <a class="btn btn-sm btn-outline-primary" href="{{ url('/opportunities') }}">Browse Opportunities</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">TOTAL HOURS</div>
          <div class="display-6 fw-semibold">hrs {{ number_format((float)$hours, 2) }}</div>
          <div class="mt-3">
            <a class="btn btn-sm btn-outline-primary" href="{{ url('/my/hours') }}">My Hours</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-lg-8">
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-white"><strong>Upcoming</strong></div>
        <div class="card-body">
          @if(!empty($applications) && count($applications))
            <ul class="mb-0">
              @foreach($applications as $a)
                <li>
                  <a href="{{ url('/opportunities/'.($a->opportunity_id ?? '')) }}">
                    {{ $a->opportunity_title ?? 'Opportunity' }}
                  </a>
                  <span class="text-muted">— {{ \Illuminate\Support\Carbon::parse($a->start_date ?? $a->date ?? now())->toFormattedDateString() }}</span>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-muted">No upcoming items yet.</div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Profile</strong></div>
        <div class="card-body">
          <div class="mb-1"><span class="text-muted">Name:</span> {{ $user->name }}</div>
          <div class="mb-1"><span class="text-muted">Email:</span> {{ $user->email }}</div>
          <div class="mt-3">
            <a class="btn btn-sm btn-outline-secondary" href="{{ url('/profile/edit') }}">Edit Profile</a>
            <form class="d-inline" method="POST" action="{{ route('logout') }}">
    @csrf
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <button type="submit" class="btn btn-sm btn-outline-danger">Sign out</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
