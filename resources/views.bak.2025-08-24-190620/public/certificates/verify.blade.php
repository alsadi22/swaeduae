@extends('layouts.app')
@section('title', __('Verify Certificate'))
@section('content')
<div class="container">
  <h3 class="mb-3">{{ __('Verify Certificate') }}</h3>
  <form class="mb-3" method="get" action="">
    <input class="form-control" name="code" value="{{ request('code') }}" placeholder="{{ __('Enter code or UUID') }}">
  </form>
  @if(isset($cert))
    @if($cert)
      <div class="alert alert-success">
        {{ __('Valid certificate') }} — ID: {{ $cert->code }} / {{ $cert->uuid }} — Hours: {{ $cert->hours }}
      </div>
    @else
      <div class="alert alert-danger">{{ __('Not found') }}</div>
    @endif
  @endif
</div>
@endsection
