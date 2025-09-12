@extends('layouts.app')

@section('title', __('Volunteer Opportunities'))

@push('head')
<style>
  /* tiny helper to clamp card text to ~3 lines */
  .text-truncate-3{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
</style>
@endpush

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 m-0">{{ __('Volunteer Opportunities') }}</h1>
    @if($opportunities?->total())
      <div class="text-muted small">
        {{ __('Showing') }} {{ $opportunities->firstItem() ?? 0 }}–{{ $opportunities->lastItem() ?? 0 }}
        {{ __('of') }} {{ $opportunities->total() ?? 0 }}
      </div>
    @endif
  </div>

  <form method="get" class="row g-2 align-items-end mb-4">
    <div class="col-12 col-md">
      <label class="form-label">{{ __('Search') }}</label>
      <input type="text" name="q" value="{{ request('q') }}" class="form-control"
             placeholder="{{ __('Keyword, skill, cause') }}">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">{{ __('Category') }}</label>
      <select name="category" class="form-select">
        <option value="">{{ __('All') }}</option>
        @foreach(($categories ?? []) as $cat)
          <option value="{{ $cat }}" @selected(request('category')==$cat)>{{ $cat }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label">{{ __('Region/Emirate') }}</label>
      <select name="region" class="form-select">
        <option value="">{{ __('All') }}</option>
        @foreach(($regions ?? []) as $reg)
          <option value="{{ $reg }}" @selected(request('region')==$reg)>{{ $reg }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary">{{ __('Apply Filters') }}</button>
    </div>
  </form>

  <div class="row g-3">
    @forelse($opportunities as $o)
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-1">{{ $o->title }}</h5>
            <div class="small text-muted mb-2">
              {{ $o->region ?? '—' }} · {{ $o->category ?? '—' }}
            </div>
            <p class="text-truncate-3 small text-muted mb-3">
              {{ $o->summary ?? \Illuminate\Support\Str::limit(strip_tags($o->description ?? ''), 160) }}
            </p>
            <a class="btn btn-sm btn-outline-primary"
               href="{{ url('/opportunities/'.$o->id) }}">
               {{ __('View Details & Apply') }}
            </a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info mb-0">{{ __('No opportunities found.') }}</div>
      </div>
    @endforelse
  </div>

  <div class="mt-4 d-flex justify-content-center">
    {{ $opportunities->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>

</div>
@endsection
