@extends('layouts.app')
@section('title', __('Server error').' | '.config('app.name'))
@section('content')
  @include('partials.page_header', ['title'=>__('Something went wrong'), 'subtitle'=>__('Please try again in a moment.')])
  <a class="btn btn-primary" href="{{ url('/') }}">{{ __('Back to home') }}</a>
@endsection
