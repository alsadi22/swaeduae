@extends('public.layout')
@section('title', __('Home'))
@section('content')
<section class="py-5"><div class="container">
  <h1 class="mb-3">{{ __('سواعد الإمارات') }}</h1>
  <p class="lead">{{ __('This page is under maintenance. Check back soon.') }}</p>
  <div class="mt-4">
    <a href="{{ route('opportunities.index') }}" class="btn btn-primary">{{ __('Browse Opportunities') }}</a>
    <a href="{{ route('events.browse') }}" class="btn btn-outline-primary ms-2">{{ __('Events') }}</a>
    <a href="{{ route('contact.show') }}" class="btn btn-outline-secondary ms-2">{{ __('Contact') }}</a>
  </div>
</div></section>
@endsection
