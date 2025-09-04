@extends('layouts.app')
@section('title', __('Payment Cancelled'))
@section('content')
<div class="container py-5">
  <div class="alert alert-warning shadow-sm">
    <strong>{{ __('Cancelled') }}</strong> {{ __('You cancelled the payment (stub).') }}
  </div>
  <a href="{{ url('/') }}" class="btn btn-outline-secondary">{{ __('Back to Home') }}</a>
</div>
@endsection
