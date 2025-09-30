@extends('public.layout')

@section('title', __('Something went wrong'))

@section('content')
<section class="section py-6">
  <div class="container centered stack">
    <span class="eyebrow">{{ __('Error 500') }}</span>
    <h1>{{ __('We hit a snag') }}</h1>
    <p class="muted">{{ __('Our team has been notified and is already working on a fix. Please refresh the page or come back in a few minutes.') }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">{{ __('Return home') }}</a>
  </div>
</section>
@endsection
