@extends('public.layout')

@section('content')
<div class="container py-4">
  <h1>{{ $event->title ?? __('Event') }}</h1>
  <p>{{ $event->description ?? __('No description provided.') }}</p>
  <p><strong>{{ __('Starts:') }}</strong> {{ $event->start_date ?? '—' }}</p>
  <p><strong>{{ __('Ends:') }}</strong> {{ $event->end_date ?? '—' }}</p>
</div>
@endsection

@push('jsonld')
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"Event",
  "name":"{{ ($event->title ?? $event->name ?? $item->title ?? 'Event')|e }}",
  "description":"{{ ($event->summary ?? $event->description ?? $item->summary ?? '')|e }}",
  "eventStatus":"https://schema.org/EventScheduled",
  "startDate":"{{ $event->start_at ?? '' }}",
  "endDate":"{{ $event->end_at ?? '' }}",
  "location":{"@type":"Place","name":"{{ ($event->location_name ?? '')|e }}","address":"{{ ($event->location ?? '')|e }}"},
  "organizer":{"@type":"Organization","name":"{{ ($event->org_name ?? 'Organizer')|e }}"},
  "url":"{{ url()->current() }}"
}
</script>
@endpush
