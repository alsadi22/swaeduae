@extends('layouts.app')
@section('title', __('Too Many Requests'))
@section('content')
<div class="container py-5">
  <div class="alert alert-info shadow-sm">
    <h1 class="h4 mb-3">{{ __('Slow down') }}</h1>
    <p class="mb-0">{{ __('You have made too many requests. Please try again in a moment.') }}</p>
  </div>
  <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-3">{{ __('Back to Home') }}</a>
</div>
@endsection
