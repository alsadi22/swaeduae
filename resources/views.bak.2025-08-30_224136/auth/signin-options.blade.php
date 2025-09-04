@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width:820px">
  <h1 class="h3 mb-4">Sign in / Register</h1>

  {{-- SSO buttons (only if configured) --}}
  @php
    $googleOn = config('services.google.client_id') && config('services.google.client_secret');
    $appleOn  = config('services.apple.client_id')  && config('services.apple.client_secret');
    $uaeOn    = config('services.uaepass.client_id') && config('services.uaepass.client_secret');
  @endphp

  @if($googleOn)
    <a class="btn btn-outline-primary w-100 mb-3" href="{{ route('social.redirect', 'google') }}">Continue with Google</a>
  @endif
  @if($appleOn)
    <a class="btn btn-outline-secondary w-100 mb-3" href="{{ route('social.redirect', 'apple') }}">Continue with Apple</a>
  @endif
  @if($uaeOn)
    <a class="btn btn-outline-dark w-100 mb-4" href="{{ route('social.redirect', 'uaepass') }}">Sign in with UAE PASS</a>
  @endif

  @if(!$googleOn && !$appleOn && !$uaeOn)
    <div class="alert alert-info">Single-sign-on is not configured yet. Use email below.</div>
  @endif

  <div class="d-flex gap-3">
    <a class="btn btn-link" href="{{ route('login.email') }}">I already have an account</a>
    <a class="btn btn-success" href="{{ route('register') }}">Create account with email</a>
  </div>
</div>
@endsection
