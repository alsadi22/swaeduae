@extends('layouts.app')
@section('title', __('My Dashboard'))
@section('content')
  <div class="container py-4">
    <h1 class="h4 mb-3">{{ __('My Dashboard') }}</h1>
    <div class="alert alert-info">
      {{ __('Your dashboard is being set up. Come back soon!') }}
    </div>
    <a class="btn btn-primary" href="{{ url('/opportunities') }}">{{ __('Browse Opportunities') }}</a>
  </div>
@endsection
