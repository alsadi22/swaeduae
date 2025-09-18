@extends('public.layout')
@section('title','Opportunities')
@section('content')
<section class="section"><div class="container">
  <h2 class="mb-3">Opportunities</h2>

  <form class="op-search mb-3" role="search" method="GET" action="{{ url('/opportunities') }}">
    <div class="row g-2">
      <div class="col-md-4">
        <input class="form-control" name="q" value="{{ $q ?? '' }}" placeholder="Search by keyword, city, skill…">
      </div>
      <div class="col-md-3">
        <select class="form-select" name="city">
          <option value="">All cities</option>
          @foreach($cities as $c)
            <option value="{{ $c }}" @selected(($city ?? '') === $c)>{{ $c }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <input type="date" class="form-control" name="date" value="{{ $date ?? '' }}">
      </div>
      <div class="col-md-2">
        <select class="form-select" name="duration">
          <option value="">Any duration</option>
          @foreach([1,2,3,4,5] as $h)
            <option value="{{ $h }}" @selected(($duration ?? '') == $h)>{{ $h }}h+</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-1 d-grid">
        <button class="btn btn-primary">Filter</button>
      </div>
    </div>
  </form>

  @php $total = method_exists($events,'total') ? $events->total() : count($events); @endphp
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="text-muted small">{{ $total }} result{{ $total==1?'':'s' }}</div>
    @if(($q ?? '') || ($city ?? '') || ($date ?? '') || ($duration ?? ''))
      <a class="small" href="{{ url('/opportunities') }}">Clear filters</a>
    @endif
  </div>

  <div class="op-grid">
    @forelse($events as $e)
      @php
        try {
          $startText = $e->starts_at ? (\Illuminate\Support\Carbon::parse($e->starts_at)->format('D, d M')) : '';
          $hoursText = ($e->starts_at && $e->ends_at)
            ? (\Illuminate\Support\Carbon::parse($e->starts_at)->diffInHours(\Illuminate\Support\Carbon::parse($e->ends_at)).'h')
            : '';
        } catch (\Throwable $ex) {
          $startText = ''; $hoursText = '';
        }
      @endphp
      <x-opportunity-card
        :title="$e->title"
        :org="''"
        :city="$e->location"
        :date="$startText"
        :hours="$hoursText"
        :link="url('/opportunities/'.$e->slug)"
      />
    @empty
      <p class="text-muted">No opportunities found.</p>
    @endforelse
  </div>

  @if(method_exists($events,'links'))
    <div class="mt-3">
      {{ $events->onEachSide(1)->links() }}
    </div>
  @endif

</div></section>
@endsection
