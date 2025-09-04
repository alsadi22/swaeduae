<!-- VOL-SOCIAL PARTIAL ACTIVE -->
<div class="mt-3">
  <a href="{{ url('/auth/google/redirect') }}" class="btn w-100 mb-2 btn-outline-secondary">{{ __('Login with Google') }}</a>

  @if (Route::has('uaepass.redirect'))
    <a href="{{ url('/auth/uaepass/redirect') }}" class="btn w-100 mb-2 btn-outline-secondary">{{ __('Login with UAE PASS') }}</a>
  @endif

  @if (config('services.facebook.client_id'))
    <a href="{{ url('/auth/facebook/redirect') }}" class="btn w-100 mb-2 btn-outline-secondary">{{ __('Login with Facebook') }}</a>
  @endif
</div>
