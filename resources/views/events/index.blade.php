@extends('layouts.app')

@section('title', __('Events'))

@section('content')
<section id="main" tabindex="-1" class="container py-4">
  <h1 class="mb-3">{{ __('Events') }}</h1>

  <form method="get" action="{{ route('events.index') }}" class="mb-4">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search events…') }}">
    <button type="submit">{{ __('Search') }}</button>
  </form>

  @if ($error)
    <div class="alert alert-warning">{{ __('We’re preparing events. Please check back soon.') }}</div>
  @endif

  @if ($events instanceof \Illuminate\Contracts\Pagination\Paginator && $events->count())
    <ul class="list-unstyled">
      @foreach ($events as $ev)
        <li class="mb-2">
          <a href="{{ route('events.show', $ev->slug ?? $ev->id) }}">
            {{ $ev->title ?? __('Untitled Event') }}
          </a>
          @if(!empty($ev->date)) <small>— {{ \Illuminate\Support\Carbon::parse($ev->date)->toFormattedDateString() }}</small> @endif
        </li>
      @endforeach
    </ul>
    {{ $events->withQueryString()->links() }}
  @else
    <p class="text-muted">{{ __('No events to show yet.') }}</p>
  @endif
</section>
@endsection
