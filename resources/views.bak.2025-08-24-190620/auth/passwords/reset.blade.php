@extends('layouts.app')
@section('content')
<div class="container py-5">
  <h1 class="h4 mb-3">{{ __('Reset Password') }}</h1>
  <form method="POST" action="{{ route('password.update') }}" class="card card-body shadow-sm">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3">
      <label for="email" class="form-label">{{ __('Email address') }}</label>
      <input id="email" type="email" name="email" value="{{ old('email', $email ?? request('email')) }}" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">{{ __('New password') }}</label>
      <input id="password" type="password" name="password" required class="form-control">
    </div>
    <div class="mb-3">
      <label for="password_confirmation" class="form-label">{{ __('Confirm password') }}</label>
      <input id="password_confirmation" type="password" name="password_confirmation" required class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
  </form>
</div>
@endsection
