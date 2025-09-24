@extends('public.layout')
@section('title','Show.Blade')
@section('content')
<section class="py-16"><div class="wrap">
@extends('public.layout')
@section('title', $op->title ?? 'Opportunity')
@section('content')
<section class="section"><div class="container" style="max-width:960px">
  <a class="btn btn-sm btn-outline-secondary mb-3" href="{{ url('/opportunities') }}">&larr; All opportunities</a>
  <h2 class="mb-2">{{ $op->title ?? 'Opportunity' }}</h2>
  <p class="text-muted mb-3">
    <span>{{ $op->location ?? '—' }}</span>
    @if($op->starts_at) • {{ \Illuminate\Support\Carbon::parse($op->starts_at)->format('Y-m-d H:i') }} @endif
    @if($op->ends_at) – {{ \Illuminate\Support\Carbon::parse($op->ends_at)->format('Y-m-d H:i') }} @endif
  </p>
  @auth
  <form method="POST" action="{{ url('/opportunities/'.$op->slug.'/apply') }}" class="mt-2 mb-4">
    @csrf
    <button class="btn btn-primary btn-sm">Apply</button>
    <a class="btn btn-outline-secondary btn-sm" href="{{ url('/ics/'.$op->slug) }}">ICS</a>
  </form>
  @else
  <p><a class="btn btn-outline-primary btn-sm" href="{{ url('/login') }}">Sign in to apply</a></p>
  @endauth
  <div class="card"><div class="card-body"><p>No description yet.</p></div></div>
</div></section>
@endsection

</div></section>
@endsection

{{-- Similar opportunities (placeholder; replace with real data when available) --}}
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 my-10">
  <h2 class="text-xl font-semibold mb-4">Similar opportunities</h2>
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach(($similar ?? []) as $s)
      <a class="block border rounded-xl p-4 hover:shadow transition"
         href="{{ url('/opportunities/'.($s->id ?? $s['id'] ?? '#')) }}">
        <div class="font-medium mb-1">{{ $s->title ?? $s['title'] ?? 'Opportunity' }}</div>
        <div class="text-sm text-muted">{{ $s->location ?? $s['location'] ?? '' }}</div>
      </a>
    @endforeach
    @if(empty($similar))
      <div class="text-sm text-muted">Similar items will appear here.</div>
    @endif
  </div>
</section>

@push('jsonld')
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"Event",
  "name":"{{ ($opportunity->title ?? $opportunity->name ?? $op->title ?? $item->title ?? 'Volunteer Opportunity')|e }}",
  "description":"{{ ($opportunity->summary ?? $opportunity->description ?? $op->summary ?? $item->summary ?? '')|e }}",
  "eventStatus":"https://schema.org/EventScheduled",
  "eventAttendanceMode":"https://schema.org/OfflineEventAttendanceMode",
  "startDate":"{{ $opportunity->start_at ?? $op->start_at ?? '' }}",
  "endDate":"{{ $opportunity->end_at ?? $op->end_at ?? '' }}",
  "location":{"@type":"Place","name":"{{ ($opportunity->location_name ?? $op->location_name ?? '')|e }}","address":"{{ ($opportunity->location ?? $op->location ?? '')|e }}"},
  "organizer":{"@type":"Organization","name":"{{ ($opportunity->org_name ?? $op->org_name ?? 'Organizer')|e }}"},
  "url":"{{ url()->current() }}"
}
</script>
@endpush
