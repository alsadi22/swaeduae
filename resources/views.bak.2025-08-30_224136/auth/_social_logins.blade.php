{{-- Swaed guard start: volunteer-only social stub --}}
@php
  $isOrg = request()->is('org/*') || request()->routeIs('org.*');
  $isVolunteer = request()->query('type') === 'volunteer';
@endphp
@if (!$isOrg && $isVolunteer)
<div class="mt-4">
  <p class="text-muted mb-2">Quick sign-in (coming soon)</p>
  <div class="d-grid gap-2">
    <button type="button" class="btn btn-outline-secondary" disabled>Sign in with Google</button>
    <button type="button" class="btn btn-outline-secondary" disabled>Sign in with UAE PASS</button>
  </div>
</div>
@endif
