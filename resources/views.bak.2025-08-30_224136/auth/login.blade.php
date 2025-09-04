@extends('layouts.auth')
@section('content')
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h4 mb-3">Sign in</h1>
        <form method="POST" action="{{ route('login') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" required autofocus value="{{ old('email') }}">
            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input name="password" type="password" class="form-control" required>
            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
          </div>
          <button class="btn btn-primary w-100" type="submit">Sign in</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
<div class="mt-3" style="display:flex;gap:.5rem;flex-wrap:wrap">
  <a class="btn btn-primary" href="/auth/google/redirect">Continue with Google</a>
  <a class="btn btn-primary" href="/auth/facebook/redirect">Continue with Facebook</a>
</div>
