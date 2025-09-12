@extends('layouts.public')

@section('content')
  @php($tiles = $tiles ?? ($opps ?? collect()))
  <section class="hero">
    <h1>{{ __('app.site.name') }}</h1>
    <p>Find verified volunteer opportunities across the UAE.</p>
    <form class="search" method="get" action="{{ route('events.browse') }}">
      <input type="text" name="q" placeholder="Search opportunities..." />
      <button type="submit" class="btn btn-primary">Search</button>
    </form>
  </section>

  <section>
    <h2>Featured & Upcoming</h2>
    <div class="grid">
      @forelse($opps as $o)
        <article class="card">
          <h3>
            <a href="{{ url('/opportunities/'.$o->id.'-'.\Illuminate\Support\Str::slug($o->title ?? 'opportunity')) }}">
              {{ $o->title ?? 'Opportunity #'.$o->id }}
            </a>
          </h3>
          <p class="muted">
            @if(!empty($o->city)) {{ $o->city }} · @endif
            @if(!empty($o->emirate)) {{ $o->emirate }} @endif
          </p>
          @if(!empty($o->start_at))
            <p class="muted">Starts: {{ \Illuminate\Support\Carbon::parse($o->start_at)->toFormattedDateString() }}</p>
          @endif
          <a class="btn btn-sm" href="{{ url('/opportunities/'.$o->id.'-'.\Illuminate\Support\Str::slug($o->title ?? 'opportunity')) }}">View</a>
        </article>
      @empty
        <p class="muted">No opportunities yet.</p>
      @endforelse
    </div>
  </section>
@endsection
