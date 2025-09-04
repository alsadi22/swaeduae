@php $__orgAuth = request()->is('org/login') || request()->is('org/register'); @endphp

<nav class="navbar navbar-expand-lg border-bottom shadow-sm" aria-label="Main">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/') }}">SawaedUAE</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="mainNav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="{{ url('/opportunities') }}" id="oppsMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{ __('Opportunities') }}
            @if((isset($navLatestOpportunities) ($navLatestOpportunities ?? collect())->count() > 0($navLatestOpportunities ?? collect())->count() > 0 $navLatestOpportunities instanceof IlluminateSupportCollection ($navLatestOpportunities ?? collect())->count() > 0($navLatestOpportunities ?? collect())->count() > 0 $navLatestOpportunities->count() > 0))
              <span class="badge rounded-pill text-bg-info">{{ ($navLatestOpportunities ?? collect())->count() }}</span>
            @endif
          </a>
          @if((isset($navLatestOpportunities) ($navLatestOpportunities ?? collect())->count() > 0($navLatestOpportunities ?? collect())->count() > 0 $navLatestOpportunities instanceof IlluminateSupportCollection ($navLatestOpportunities ?? collect())->count() > 0($navLatestOpportunities ?? collect())->count() > 0 $navLatestOpportunities->count() > 0))
            <ul class="dropdown-menu" aria-labelledby="oppsMenu">
              @foreach($navLatestOpportunities as $o)
                <li><a class="dropdown-item" href="{{ url('/opportunities/'.$o->id) }}">{{ Str::limit($o->title, 42) }}</a></li>
              @endforeach
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="{{ url('/opportunities') }}">{{ __('Browse all') }} →</a></li>
            </ul>
          @endif
        </li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/events') }}">{{ __('Events') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/gallery') }}">{{ __('Gallery') }}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/verify/SAMPLE') }}">{{ __('Verify') }}</a></li>
      </ul>

      <div class="d-flex gap-2">
        @auth
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('profile') }}">{{ __('Dashboard') }}</a>
          <a class="btn btn-sm btn-brand" href="{{ route('my.certificates') }}">{{ __('My Certificates') }}</a>
          <form class="ms-2" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Sign out') }}</button>
          </form>
        @else
    @include('partials.auth_dropdown') {{-- auth dropdown --}}
        @endauth
      </div>
    </div>
  </div>
</nav>
{{-- marker nav.blade.php 04:04:24 --}}
@endif {{-- ORG_AUTH_GUARD --}}
