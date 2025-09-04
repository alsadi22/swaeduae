@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('My Volunteer Hours') }}</h1>
  <div class="alert alert-info">
    {{ __('This page is under construction. Your recorded hours will appear here soon.') }}
  </div>
  <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">{{ __('Back') }}</a>
</div>
@endsection
