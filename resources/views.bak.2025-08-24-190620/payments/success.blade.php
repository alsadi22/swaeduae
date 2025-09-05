@extends('layouts.app')
@section('title', __('Payment Success'))
@section('content')
<div class="container py-5">
  <div class="alert alert-success shadow-sm">
    <strong>{{ __('Success!') }}</strong> {{ __('Payment flow simulated (stub).') }}
  </div>
  <a href="{{ url('/') }}" class="btn btn-outline-secondary">{{ __('Back to Home') }}</a>
</div>
@endsection
