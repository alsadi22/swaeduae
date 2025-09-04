@extends('layouts.app')

@section('title', __('Verify your email'))

@section('content')
<div class="container py-5" style="max-width:700px;">
  <h1 class="mb-3">{{ __('Verify your email address') }}</h1>
  @if (session('status') === 'verification-link-sent')
    <div class="alert alert-success">{{ __('A new verification link has been sent to your email address.') }}</div>
  @endif
  <p>{{ __('Before continuing, please check your email for a verification link.') }}</p>
  <p>{{ __('If you did not receive the email, you can request another:') }}</p>
  <form method="POST" action="{{ route('verification.send') }}" class="mt-3">
    @csrf
    <button type="submit" class="btn btn-primary">{{ __('Resend verification email') }}</button>
    <a href="{{ route('profile') }}" class="btn btn-outline-secondary ms-2">{{ __('Go to profile') }}</a>
  </form>
</div>
@endsection
