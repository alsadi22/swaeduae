@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Organization Dashboard</h1>
  <p>Welcome, {{ auth()->user()->organization->name ?? auth()->user()->name }}</p>
  <div class="row">
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-body text-center">
          <h5>Total Events</h5>
          <p class="display-6">{{ $events->count() }}</p>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-body text-center">
          <h5>Total Opportunities</h5>
          <p class="display-6">{{ $opportunities->count() }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <h4>Recent Applications</h4>
      <ul class="list-group">
        @forelse($applications as $application)
          <li class="list-group-item d-flex justify-content-between">
            <div>
              {{ $application->event->title ?? $application->opportunity->title }}<br>
              <small>Applicant: {{ $application->user->name }}</small>
            </div>
            <a href="#" class="btn btn-outline-success btn-sm">View</a>
          </li>
        @empty
          <li class="list-group-item text-muted">No applications yet.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection
