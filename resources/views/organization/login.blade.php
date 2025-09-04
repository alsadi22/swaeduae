@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
      <div class="card shadow border-0">
        <div class="card-body">
          <form method="POST" action="{{ url('/org/login') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">{{ __('Business Email') }}</label>
              <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">{{ __('Password') }}</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
              </div>
              <a href="{{ route('password.request') }}" class="text-sm">{{ __('Forgot password?') }}</a>
            </div>
            <button class="btn btn-primary w-100" type="submit">{{ __('Login') }}</button>
<div class="text-center text-sm mt-2">@if(\Illuminate\Support\Facades\Route::has('password.request'))<a href="{{ route('password.request') }}">Forgot your password?</a>@else<a href="/forgot-password">Forgot your password?</a>@endif</div>
</form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
