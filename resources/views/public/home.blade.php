@extends('public.layout')

@section('content')
@extends('public.layout-travelpro')
@section('title', __('Home'))
@section('meta_description','Connecting volunteers with opportunities across the UAE.')
@section('content')
<section class="py-5"><div class="container">
  <h1 class="mb-3">{{ __('سواعد الإمارات') }}</h1>
  <p class="lead">{{ __('Connecting volunteers with opportunities across the UAE.') }}</p>
  <div class="mt-4">
    <a href="{{ route('opportunities.index') }}" class="btn btn-primary">{{ __('Browse Opportunities') }}</a>
    <a href="{{ route('events.browse') }}" class="btn btn-outline-primary ms-2">{{ __('Events') }}</a>
    <a href="{{ Route::has('contact.get') ? route('contact.get') : url('/contact') }}" class="btn btn-outline-secondary ms-2">{{ __('Contact') }}</a>
  </div>
</div></section>
@endsection
