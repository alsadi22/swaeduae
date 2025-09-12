@extends('layouts.app')
@section('title', __('Regions'))
@section('content')
<div class="container py-4">
  <h1 class="mb-4">{{ __('Regions') }}</h1>
  <div class="row">
    @foreach($regions as $r)
      <div class="col-md-4 mb-3">
        <a class="card p-3 h-100" href="{{ url('/regions/'.$r['slug']) }}">
          <h5 class="mb-1">{{ $r['name'] }}</h5>
          <div class="text-muted">{{ __('See opportunities') }}</div>
        </a>
      </div>
    @endforeach
  </div>
</div>
@endsection
