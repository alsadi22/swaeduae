@extends('public.layout')

@php
  $pageTitle = $event->title ?? __('Event');
@endphp

@section('title', $pageTitle)

@section('content')
<section class="section py-6">
  <div class="container stack max-w-3xl">
    <a class="back-link" href="{{ url('/events') }}">&larr; {{ __('Back to events') }}</a>

    <article class="event-detail stack">
      <header class="stack-sm">
        <span class="eyebrow">{{ __('Event spotlight') }}</span>
        <h1>{{ $pageTitle }}</h1>
        @if (!empty($event->summary))
          <p class="muted">{{ $event->summary }}</p>
        @endif
      </header>

      <div class="meta-grid">
        @php
          $start = $event->start_date ?? $event->starts_at ?? null;
          $end = $event->end_date ?? $event->ends_at ?? null;
          $location = $event->location ?? $event->venue ?? null;
          $hours = $event->hours ?? $event->duration ?? null;
        @endphp
        @if ($start)
          <span>
            <svg width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M6 1h1v2h2V1h1v2h2a1 1 0 0 1 1 1v2H3V4a1 1 0 0 1 1-1h2V1Zm7 6v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7h10Zm-7 2H5v2h1V9Zm2 0H7v2h1V9Zm2 0h-1v2h1V9Z"/></svg>
            <strong>{{ __('Starts:') }}</strong> {{ $start }}
          </span>
        @endif
        @if ($end)
          <span>
            <svg width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M2.5 3A1.5 1.5 0 0 1 4 1.5h8A1.5 1.5 0 0 1 13.5 3v10A1.5 1.5 0 0 1 12 14.5H4A1.5 1.5 0 0 1 2.5 13V3Zm1.5-.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5H4Z"/></svg>
            <strong>{{ __('Ends:') }}</strong> {{ $end }}
          </span>
        @endif
        @if ($location)
          <span>
            <svg width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M8 1.5a4.5 4.5 0 0 1 4.5 4.5c0 2.04-1.33 4.16-3.66 6.55a1.3 1.3 0 0 1-1.68.17l-.18-.17C4.67 10.16 3.5 8.04 3.5 6A4.5 4.5 0 0 1 8 1.5Zm0 2a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z"/></svg>
            <strong>{{ __('Location:') }}</strong> {{ $location }}
          </span>
        @endif
        @if ($hours)
          <span>
            <svg width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M8 1.5a6.5 6.5 0 1 1 0 13 6.5 6.5 0 0 1 0-13Zm0 1a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Zm-.5 1.5h1v4l2.5 2.5-.7.7L7.5 8V4Z"/></svg>
            <strong>{{ __('Volunteer hours:') }}</strong> {{ $hours }}
          </span>
        @endif
      </div>

      <div class="stack-sm">
        @if (!empty($event->description))
          <div>{!! nl2br(e($event->description)) !!}</div>
        @else
          <p class="muted">{{ __('Details for this event will be announced soon. Please check back for updates.') }}</p>
        @endif

        @if (!empty($event->notes))
          <div class="callout">{{ $event->notes }}</div>
        @endif
      </div>

      @if (!empty($event->cta_url))
        <div>
          <a class="btn btn-primary" href="{{ $event->cta_url }}" target="_blank" rel="noopener">{{ $event->cta_label ?? __('Register now') }}</a>
        </div>
      @endif
    </article>
  </div>
</section>
@endsection
