@extends(view()->exists('layouts.org') ? 'layouts.org' : (view()->exists('layouts.app') ? 'layouts.app' : null))
@section('content')
<div class="container py-4">
  <form method="POST" action="/org/login">
    @csrf
    <div class="mb-3">
      <label class="form-label">{{ __('Email') }}</label>
      <input class="form-control" type="email" name="email" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">{{ __('Password') }}</label>
      <input class="form-control" type="password" name="password" required>
    </div>
    <button class="btn btn-primary">{{ __('Sign in') }}</button>
<div class="text-center text-sm mt-2">@if(\Illuminate\Support\Facades\Route::has('password.request'))<a href="{{ route('password.request') }}">Forgot your password?</a>@else<a href="/forgot-password">Forgot your password?</a>@endif</div>
</form>
</div>
@endsection
