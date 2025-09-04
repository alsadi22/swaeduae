@extends('layouts.app')
@section('title', $region)
@section('content')
<div class="container py-4">
  <h1 class="mb-4">{{ $region }}</h1>
  @if($opportunities->count())
    <div class="list-group">
      @foreach($opportunities as $o)
        <a class="list-group-item list-group-item-action" href="{{ url('/opportunities/'.$o->id) }}">
          <div class="d-flex justify-content-between">
            <div><strong>{{ $o->title ?? __('Opportunity') }}</strong></div>
            <small class="text-muted">#{{ $o->id }}</small>
          </div>
        </a>
      @endforeach
    </div>
  @else
    <div class="alert alert-info">{{ __('No opportunities yet in this region.') }}</div>
  @endif
</div>
@endsection
