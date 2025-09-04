@extends('layouts.app')
@section('content')
<div class="container py-5">
  <h1 class="h4 mb-3">{{ __('Forgot your password?') }}</h1>
  <form method="POST" action="{{ route('password.email') }}" class="card card-body shadow-sm">
    @csrf
    <div class="mb-3">
      <label for="email" class="form-label">{{ __('Email address') }}</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Send password reset link') }}</button>
  </form>
</div>
@endsection
