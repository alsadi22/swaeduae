@extends('layouts.app')
@section('title', __('Partners').' | '.config('app.name'))

@section('page_header')
  <x-page-header :title="__('Partners')" :subtitle="__('Organizations collaborating with SawaedUAE')" />
@endsection

@section('content')
  <div class="mb-3">
    <a class="btn btn-teal" href="{{ route('organizations.register') }}">
      <i class="bi bi-building me-1"></i> {{ __('Register your organization') }}
    </a>
  </div>

  <div class="empty-state">
    <p>{{ __('Partner directory will appear here.') }}</p>
    <p class="text-muted small">{{ __('(We can wire to a real Partner model later)') }}</p>
  </div>
@endsection
