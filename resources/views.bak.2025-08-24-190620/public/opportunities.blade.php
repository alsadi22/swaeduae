@extends('layouts.app')
@section('title', __('Opportunities'))
@section('content')
<div class="container my-5">
  <h1 class="h4 mb-3">{{ __('Opportunities') }}</h1>
  <form class="mb-3" method="get">
    <input class="form-control" name="q" value="{{ $q ?? '' }}" placeholder="{{ __('Keyword, skill, cause') }}">
  </form>
  @if($rows->isEmpty())
    <div class="alert alert-light">{{ __('No opportunities yet.') }}</div>
  @else
    <div class="list-group">
      @foreach($rows as $o)
        <div class="list-group-item">
          <div class="fw-semibold">{{ $o->title }}</div>
          <div class="text-muted small">
            {{ $o->region ?? '' }}@if(!empty($o->category)) • {{ $o->category }}@endif
            @if(!empty($o->date)) • {{ $o->date }}@endif
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
