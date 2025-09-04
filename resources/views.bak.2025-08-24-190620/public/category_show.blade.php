@extends('layouts.app')
@section('content')
<div class="container">
  <h1>{{ $category->name }}</h1>
  <p>{{ $category->description }}</p>
  <h4>Opportunities</h4>
  <div class="row">
    @forelse($opportunities as $opportunity)
      <div class="col-md-4 mb-3">
        <a href="{{ route('public.opportunities.show',$opportunity->id) }}" class="btn btn-outline-success w-100">{{ $opportunity->title }}</a>
      </div>
    @empty
      <p>No opportunities in this category.</p>
    @endforelse
  </div>
  <h4>Events</h4>
  <div class="row">
    @forelse($events as $event)
      <div class="col-md-4 mb-3">
        <a href="{{ route('public.events.show',$event->id) }}" class="btn btn-outline-success w-100">{{ $event->title }}</a>
      </div>
    @empty
      <p>No events in this category.</p>
    @endforelse
  </div>
</div>
@endsection
