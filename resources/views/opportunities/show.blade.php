@extends('public.layout')
@section('title', $event->title)
@section('content')
<section class="section"><div class="container" style="max-width: 920px">
  @if (session('ok'))
    <div class="alert alert-success py-2 mb-3">{{ session('ok') }}</div>
  @endif

  <nav class="small mb-2"><a href="{{ url('/opportunities') }}">← Back to Opportunities</a></nav>
  <h1 class="h3 mb-2">{{ $event->title }}</h1>
  <div class="text-muted mb-3">{{ $event->location ?? 'UAE' }} ·
    @if($event->starts_at) {{ optional($event->starts_at)->format('D, d M Y H:i') }} @endif
    @if($event->ends_at) – {{ optional($event->ends_at)->format('H:i') }} @endif
  </div>
  <p class="mb-4">{{ $event->description }}</p>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a class="btn btn-outline-primary btn-sm" href="{{ url('/ics/'.$event->slug) }}">Add to Calendar (.ics)</a>
    <a class="btn btn-outline-secondary btn-sm" target="_blank"
       href="https://wa.me/?text={{ urlencode($event->title.' — '.url('/opportunities/'.$event->slug)) }}">Share via WhatsApp</a>
  </div>

  @auth
    @if(!empty($applied))
      <div class="alert alert-success py-2 mb-3">✅ You’ve applied for this opportunity.</div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary btn-sm" disabled>Quick Apply</button>
        <form method="POST" action="{{ url('/opportunities/'.$event->slug.'/cancel') }}">
          @csrf
          <button class="btn btn-outline-danger btn-sm">Cancel</button>
        </form>
      </div>
    @else
      <form method="POST" action="{{ url('/opportunities/'.$event->slug.'/apply') }}" class="mb-3">
        @csrf
        <button class="btn btn-primary btn-sm">Quick Apply</button>
      </form>
    @endif
  @else
    <a class="btn btn-primary btn-sm" href="{{ url('/login') }}">Sign in to apply</a>
  @endauth
</div></section>
@endsection
