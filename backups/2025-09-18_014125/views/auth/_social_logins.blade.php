@php
  $googleEnabled = config('services.google.client_id') && config('services.google.client_secret');
  $isOrgPath = request()->is('org/*');
@endphp
@if($googleEnabled && !$isOrgPath)
  <div class="mt-4">
    <a class="btn btn-outline-dark w-100" href="{{ url('/auth/google') }}">Sign in with Google</a>
  </div>
@endif