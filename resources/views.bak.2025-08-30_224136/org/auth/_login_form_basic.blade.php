@extends("org.layout")
@php(
  $orgLoginAction = \Illuminate\Support\Facades\Route::has('org.login.perform')
    ? route('org.login.perform')
    : url('/org/login')
)
<form method="POST" action="{{ url('/org/login') }}" class="mx-auto" style="max-width:480px">
  @csrf
  <div class="mb-3">
    <label class="form-label" for="org_email">{{ __('Business Email') }}</label>
    <input id="org_email" type="email" name="email" class="form-control" required autocomplete="email" value="{{ old('email') }}">
  </div>
  <div class="mb-3">
    <label class="form-label" for="org_password">{{ __('Password') }}</label>
    <input id="org_password" type="password" name="password" class="form-control" required autocomplete="current-password">
  </div>
  <button type="submit" class="btn btn-primary w-100">{{ __('Sign in to Organization') }}</button>
</form>
