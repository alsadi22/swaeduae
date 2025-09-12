@extends('layouts.app')
@section('title', __('Verify Certificate'))
@section('content')
<div class="container my-5">
  <h1 class="h4 mb-3">{{ __('Verify Certificate') }}</h1>
  <form method="get" class="row g-2 mb-3">
    <div class="col-auto"><input class="form-control" name="code" value="{{ $code ?? '' }}" placeholder="{{ __('Verification code') }}"></div>
    <div class="col-auto"><button class="btn btn-primary">{{ __('Check') }}</button></div>
  </form>
  @isset($result)
    @if($result)
      <div class="alert alert-success">{{ __('Certificate found') }} — #{{ $result->id }}</div>
    @else
      <div class="alert alert-danger m-0">{{ __('No certificate found for that code.') }}</div>
    @endif
  @endisset
</div>
@endsection
