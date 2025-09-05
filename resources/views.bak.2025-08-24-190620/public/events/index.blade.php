@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ __('Events') }}</h1>

  @if(isset($items) && $items->count())
    <div class="list-group">
      @foreach($items as $event)
        <a href="{{ route('events.show', $event->id) }}" class="list-group-item list-group-item-action">
          <h5>{{ $event->title ?? __('Untitled') }}</h5>
          <p class="mb-1">{{ Str::limit($event->description ?? '', 120) }}</p>
        </a>
      @endforeach
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
  @else
    <div class="alert alert-info">{{ __('No events available.') }}</div>
  @endif
</div>
@endsection
