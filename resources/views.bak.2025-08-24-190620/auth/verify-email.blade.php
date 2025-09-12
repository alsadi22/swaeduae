@extends('layouts.app')
@section('title', __('Verify your email'))
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card p-4">
        <h4 class="mb-3">{{ __('Verify your email') }}</h4>
        @if (session('status') === 'verification-link-sent')
          <div class="alert alert-success">{{ __('A new verification link has been sent to your email address.') }}</div>
        @endif
        <p>{{ __('Before continuing, please check your email for a verification link.') }}</p>
        <form method="post" action="{{ route('verification.send') }}" class="d-inline">
          @csrf
          <button class="btn btn-primary">{{ __('Resend verification email') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
