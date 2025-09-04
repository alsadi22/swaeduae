@php(
  $registerAction = \Illuminate\Support\Facades\Route::has('register.perform')
    ? url('/register')
    : ( \Illuminate\Support\Facades\Route::has('register')
        ? route('register')
        : url('/register') )
)
<form method="POST" action="{{ $registerAction }}" class="mx-auto" style="max-width:480px">
  @csrf
  <div class="mb-3">
    <label class="form-label" for="name">{{ __('Full Name') }}</label>
    <input id="name" type="text" name="name" class="form-control" required value="{{ old('name') }}">
  </div>
  <div class="mb-3">
    <label class="form-label" for="email">{{ __('Email') }}</label>
    <input id="email" type="email" name="email" class="form-control" required value="{{ old('email') }}">
  </div>
  <div class="mb-3">
    <label class="form-label" for="password">{{ __('Password') }}</label>
    <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
  </div>
  <div class="mb-3">
    <label class="form-label" for="password_confirmation">{{ __('Confirm Password') }}</label>
    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
  </div>
  <button type="submit" class="btn btn-success w-100">{{ __('Create Volunteer Account') }}</button>
</form>
