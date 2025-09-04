@extends('layouts.app')

@section('title', 'Events – Browse')

@section('content')
<div class="container py-5">
  <h1 class="mb-4">Upcoming Events</h1>

  @php $items = $items ?? ($events ?? collect()); @endphp

  @if($items->isEmpty())
    <p>No events found yet. Please check back soon.</p>
  @else
    <div class="row g-3">
      @foreach($items as $event)
        <div class="col-md-4">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ $event->title ?? 'Untitled event' }}</h5>
              <p class="card-text">{{ \Illuminate\Support\Str::limit($event->description ?? '', 120) }}</p>
            </div>
            <div class="card-footer">
              <a class="btn btn-primary btn-sm" href="{{ url('events/'.($event->slug ?? $event->id)) }}">View</a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
  @endif
</div>
@endsection
