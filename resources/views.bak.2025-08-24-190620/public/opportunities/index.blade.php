@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ __('Opportunities') }}</h1>

  @if(isset($items) && $items->count())
    <div class="list-group">
      @foreach($items as $opp)
        <a href="{{ route('opportunities.show', $opp->id) }}" class="list-group-item list-group-item-action">
          <h5>{{ $opp->title ?? __('Untitled') }}</h5>
          <p class="mb-1">{{ Str::limit($opp->description ?? '', 120) }}</p>
        </a>
      @endforeach
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
  @else
    <div class="alert alert-info">{{ __('No opportunities available.') }}</div>
  @endif
</div>
@endsection
