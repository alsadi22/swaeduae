<input type="hidden" name="type" value="{{ request('type') }}">
@php(
  $loginAction = \Illuminate\Support\Facades\Route::has('login.perform')
    ? route('login.perform', request()->only('type'))
    : ( \Illuminate\Support\Facades\Route::has('login')
        ? route('login', request()->only('type'))
        : url('/login') )
)
<form method="POST" action="{{ $loginAction }}" class="mx-auto" style="max-width:480px">
  @csrf
  <div class="mb-3">
    <label class="form-label" for="email">{{ __('Email') }}</label>
    <input id="email" type="email" name="email" class="form-control" required autocomplete="email" value="{{ old('email') }}">
  </div>
  <div class="mb-3">
    <label class="form-label" for="password">{{ __('Password') }}</label>
    <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
  </div>
  <button type="submit" class="btn btn-primary w-100">{{ __('Sign in') }}</button>
  <div class="mt-3 text-center">
    <a class="small text-muted" href="{{ route('password.request') }}">Forgot your password?</a>
  </div>
</form>