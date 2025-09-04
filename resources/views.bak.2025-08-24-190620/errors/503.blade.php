@extends('layouts.app')
@section('title', __('Maintenance'))
@section('content')
<div class="container py-5">
  <div class="alert alert-secondary shadow-sm">
    <h1 class="h4 mb-3">{{ __('We’ll be right back') }}</h1>
    <p class="mb-0">{{ __('The site is under scheduled maintenance. Please check back soon.') }}</p>
  </div>
  <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-3">{{ __('Back to Home') }}</a>
</div>
@endsection
