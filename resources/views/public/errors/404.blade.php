@extends('public.layout')

@section('title', __('Page not found'))

@section('content')
<section class="section py-6">
  <div class="container centered stack">
    <span class="eyebrow">{{ __('Error 404') }}</span>
    <h1>{{ __('We could not find that page') }}</h1>
    <p class="muted">{{ __('The page you are looking for may have been moved or no longer exists. You can return home to continue exploring SwaedUAE.') }}</p>
    <a class="btn btn-primary" href="{{ url('/') }}">{{ __('Back to homepage') }}</a>
  </div>
</section>
@endsection
