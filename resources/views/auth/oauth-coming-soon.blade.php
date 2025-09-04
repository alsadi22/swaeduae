@extends('layouts.app')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <h1 class="h4 mb-3">{{ $provider ?? 'Social Login' }} – Coming Soon</h1>
          <p class="mb-4">We’re finalizing {{ $provider ?? 'this' }} sign-in. Please use email and password for now.</p>
          <a href="{{ route('login') }}" class="btn btn-primary">Back to Sign in</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
