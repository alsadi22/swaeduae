@extends('layouts.app')
@section('title', __('Page not found').' | '.config('app.name'))
@section('content')
  @include('partials.page_header', ['title'=>__('Page not found'), 'subtitle'=>__("Sorry, we couldn’t find that page.")])
  <a class="btn btn-primary" href="{{ url('/') }}">{{ __('Back to home') }}</a>
@endsection
