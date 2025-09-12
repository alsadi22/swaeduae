@extends('layouts.app')
@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ __('Opportunities') }}</h1>
    <form class="d-flex" method="get" action="{{ url('/opportunities') }}" role="search">
      <input name="q" class="form-control form-control-sm me-2" placeholder="{{ __('Search') }}" value="{{ $q }}">
      <button class="btn btn-primary btn-sm">{{ __('Search') }}</button>
    </form>
  </div>

  @if($opportunities->isEmpty())
    <div class="alert alert-info">{{ __('No opportunities found.') }}</div>
  @else
    <div class="row g-3">
      @foreach($opportunities as $opp)
        <div class="col-md-4">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex flex-column">
              <h2 class="h6 mb-1">{{ $opp->title }}</h2>
              @if(!empty($opp->location))
                <div class="text-muted small mb-2">{{ $opp->location }}</div>
              @endif
              @php
                $desc = isset($opp->description) ? strip_tags($opp->description) : '';
                $excerpt = mb_substr($desc, 0, 160);
              @endphp
              <p class="small text-muted mb-3">{{ $excerpt }}</p>
              <div class="mt-auto">
                <a class="btn btn-outline-primary btn-sm" href="{{ route('public.opportunity.show', ['id'=>$opp->id]) }}">{{ __('View details') }}</a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $opportunities->links() }}
    </div>
  @endif
</div>
@endsection
