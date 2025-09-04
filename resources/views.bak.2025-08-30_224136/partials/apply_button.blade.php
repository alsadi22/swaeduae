{{-- Safe auth buttons: only render if the route exists --}}
@if (Route::has('login'))
  <a href="{{ route('login') }}">@lang('Login')</a>
@endif

@if (Route::has('register'))
  <a href="{{ route('register') }}">@lang('Register')</a>
@endif

@if (Route::has('password.request'))
  <a href="{{ route('password.request') }}">@lang('Forgot password?')</a>
@endif
