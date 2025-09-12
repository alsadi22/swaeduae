<style>
  @media (min-width: 992px){ .v-aside.sticky-lg { position: sticky; top: 88px; } }
</style>
<div class="d-none d-lg-block v-aside sticky-lg">
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><strong>{{ __('Volunteer') }}</strong></div>
    <div class="list-group list-group-flush">
      <a href="{{ route('profile') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile') ? 'active' : '' }}">{{ __('Dashboard') }}</a>
      <a href="{{ \Illuminate\Support\Facades\Route::has('my.certificates') ? route('my.certificates') : url('/my/certificates') }}" class="list-group-item list-group-item-action">{{ __('My Certificates') }}</a>
      @if (\Illuminate\Support\Facades\Route::has('my.hours'))
        <a href="{{ route('my.hours') }}" class="list-group-item list-group-item-action">{{ __('My Hours') }}</a>
      @endif
      <a href="{{ url('/opportunities') }}" class="list-group-item list-group-item-action">{{ __('Browse Opportunities') }}</a>
      <a href="{{ url('/') }}" class="list-group-item list-group-item-action">{{ __('View Site') }}</a>
      <form method="POST" action="{{ route('logout') }}" class="list-group-item p-0 border-0">
        @csrf
        <button class="btn btn-link w-100 text-start px-3 py-2 list-group-item-action"><span class="text-danger">{{ __('Sign out') }}</span></button>
      </form>
    </div>
  </div>
</div>

<button class="btn btn-outline-primary d-lg-none mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#volSidebarCanvas">
  {{ __('Menu') }}
</button>
<div class="offcanvas offcanvas-start" tabindex="-1" id="volSidebarCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">{{ __('Volunteer') }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="list-group list-group-flush">
      <a href="{{ route('profile') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile') ? 'active' : '' }}">{{ __('Dashboard') }}</a>
      <a href="{{ \Illuminate\Support\Facades\Route::has('my.certificates') ? route('my.certificates') : url('/my/certificates') }}" class="list-group-item list-group-item-action">{{ __('My Certificates') }}</a>
      @if (\Illuminate\Support\Facades\Route::has('my.hours'))
        <a href="{{ route('my.hours') }}" class="list-group-item list-group-item-action">{{ __('My Hours') }}</a>
      @endif
      <a href="{{ url('/opportunities') }}" class="list-group-item list-group-item-action">{{ __('Browse Opportunities') }}</a>
      <a href="{{ url('/') }}" class="list-group-item list-group-item-action">{{ __('View Site') }}</a>
      <form method="POST" action="{{ route('logout') }}" class="list-group-item p-0 border-0">
        @csrf
        <button class="btn btn-link w-100 text-start px-3 py-2 list-group-item-action"><span class="text-danger">{{ __('Sign out') }}</span></button>
      </form>
    </div>
  </div>
</div>
