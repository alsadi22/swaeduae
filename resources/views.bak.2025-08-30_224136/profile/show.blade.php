@extends('layouts.app')
@section('content')
<div class="container py-4">
  <h1 class="h3 mb-3">{{ __('My Profile') }}</h1>
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <p class="mb-1"><strong>{{ __('Name') }}:</strong> {{ $user->name ?? '-' }}</p>
      <p class="mb-1"><strong>{{ __('Email') }}:</strong> {{ $user->email ?? '-' }}</p>
    </div>
  </div>
</div>
@endsection
