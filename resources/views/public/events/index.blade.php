@extends('public.layout')

@section('title', __('Events'))

@section('content')
<section class="section py-6">
  <div class="container stack">
    <div class="stack-sm max-w-xl">
      <p class="eyebrow">{{ __('Community Events') }}</p>
      <h1>{{ __('Upcoming Events') }}</h1>
      <p class="muted">{{ __('Join our upcoming activities, trainings, and community gatherings hosted across the Emirates.') }}</p>
    </div>

    @php
      $records = $events ?? ($items ?? []);
      $isPaginator = $records instanceof \Illuminate\Contracts\Pagination\Paginator;
      $list = $isPaginator ? collect($records->items()) : collect($records);
    @endphp

    @if ($list->isEmpty())
      <div class="form-card stack-sm">
        <h2>{{ __('Stay tuned!') }}</h2>
        <p class="muted">{{ __('We are finalising new volunteer experiences. Check back soon or follow us on social for announcements.') }}</p>
        <a class="btn btn-primary" href="{{ url('/') }}">{{ __('Return home') }}</a>
      </div>
    @else
      <div class="cards">
        @foreach ($isPaginator ? $records : $list as $event)
          @php
            $start = $event->start_date ?? $event->starts_at ?? null;
            $end = $event->end_date ?? $event->ends_at ?? null;
            $location = $event->location ?? $event->venue ?? null;
            $updatedAt = $event->updated_at ?? null;
            $updatedLabel = null;

            if ($updatedAt instanceof \Carbon\CarbonInterface) {
              $updatedLabel = $updatedAt->diffForHumans();
            } elseif (is_object($updatedAt) && method_exists($updatedAt, 'diffForHumans')) {
              $updatedLabel = $updatedAt->diffForHumans();
            } elseif (is_string($updatedAt)) {
              $updatedLabel = $updatedAt;
            }

            $link = url('events/' . ($event->slug ?? $event->id ?? ''));
          @endphp
          <article class="card event-card">
            <div class="stack-sm">
              <h2>{{ $event->title ?? __('Untitled event') }}</h2>
              @if (!empty($event->description))
                <p class="muted">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 140) }}</p>
              @endif
            </div>

            <div class="meta-grid">
              @if ($start)
                <span>
                  <svg width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M6 1h1v2h2V1h1v2h2a1 1 0 0 1 1 1v2H3V4a1 1 0 0 1 1-1h2V1Zm7 6v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7h10Zm-7 2H5v2h1V9Zm2 0H7v2h1V9Zm2 0h-1v2h1V9Z"/></svg>
                  <strong>{{ __('Starts:') }}</strong> {{ $start }}
                </span>
              @endif
              @if ($end)
                <span>
                  <svg width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M2.5 3A1.5 1.5 0 0 1 4 1.5h8A1.5 1.5 0 0 1 13.5 3v10A1.5 1.5 0 0 1 12 14.5H4A1.5 1.5 0 0 1 2.5 13V3Zm1.5-.5a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5H4Z"/></svg>
                  <strong>{{ __('Ends:') }}</strong> {{ $end }}
                </span>
              @endif
              @if ($location)
                <span>
                  <svg width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M8 1.5a4.5 4.5 0 0 1 4.5 4.5c0 2.04-1.33 4.16-3.66 6.55a1.3 1.3 0 0 1-1.68.17l-.18-.17C4.67 10.16 3.5 8.04 3.5 6A4.5 4.5 0 0 1 8 1.5Zm0 2a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z"/></svg>
                  <strong>{{ __('Location:') }}</strong> {{ $location }}
                </span>
              @endif
            </div>

            <div class="card-footer">
              <a class="btn" href="{{ $link }}">{{ __('View details') }}</a>
              <span class="muted">{{ __('Last updated') }} {{ $updatedLabel ?? __('Recently added') }}</span>
            </div>
          </article>
        @endforeach
      </div>

      @if ($isPaginator)
        <div class="mt-4">
          {{ $records->links() }}
        </div>
      @endif
    @endif
  </div>
</section>
@endsection
