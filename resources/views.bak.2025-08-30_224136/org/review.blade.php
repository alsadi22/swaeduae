@extends('org.layout')
@section('title', __('Application under review'))

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h3 class="mb-2">{{ __('Your application is under review') }}</h3>
          <p class="text-muted mb-3">
            {{ __('Thank you for registering your organization on SawaedUAE. Our team is reviewing the information and license you provided.') }}
          </p>
          <ul class="mb-3">
            <li>{{ __('You’ll receive an email when your account is approved or if we need more information.') }}</li>
            <li>{{ __('You must verify your email address via the verification link sent to your inbox.') }}</li>
          </ul>
          <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Back to home') }}</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
