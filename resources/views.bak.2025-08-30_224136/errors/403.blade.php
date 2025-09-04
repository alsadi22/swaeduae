@extends('layouts.app')
@section('title', __('Forbidden'))
@section('content')
<div class="container py-5">
  <div class="alert alert-warning shadow-sm">
    <h1 class="h4 mb-3">{{ __('Access denied') }}</h1>
    <p class="mb-0">{{ __('You do not have permission to access this page.') }}</p>
  </div>
  <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-3">{{ __('Back to Home') }}</a>
</div>
@endsection
