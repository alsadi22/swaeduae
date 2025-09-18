@php
  $is = function($p){ foreach((array)$p as $x){ if(request()->routeIs($x)||request()->is($x)) return 'active'; } return ''; };
@endphp
<div class="position-sticky z-index-sticky top-0">
  <nav class="navbar navbar-expand-lg bg-white shadow-sm border-0 px-3 py-1 mx-2 my-2 rounded-3">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="{{ url('/') }}">SawaedUAE</a>

      <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarPublic" aria-controls="navbarPublic" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Full width collapse; nav centered -->
      <div class="collapse navbar-collapse justify-content-center w-100" id="navbarPublic">
        <ul class="navbar-nav align-items-lg-center gap-lg-3">
          <li class="nav-item"><a class="nav-link {{ $is('verify*') }}" href="{{ url('/verify') }}">@lang('Verify')</a></li>
          <li class="nav-item"><a class="nav-link {{ $is('gallery*') }}" href="{{ url('/gallery') }}">@lang('Gallery')</a></li>
          <li class="nav-item"><a class="nav-link {{ $is('events*') }}" href="{{ url('/events') }}">@lang('Events')</a></li>
          <li class="nav-item"><a class="nav-link {{ $is(['opportunities*','public/opportunities*']) }}" href="{{ url('/opportunities') }}">@lang('Opportunities')</a></li>

          @auth
            <li class="nav-item"><a class="nav-link {{ $is(['profile*','my*','dashboard*']) }}" href="{{ (auth()->check() && (method_exists(auth()->user(),'hasRole') ? auth()->user()?->hasRole('org') : request()->is('org*'))) ? route('org.dashboard.guard.wrap') : route('profile') }}">@lang('My Dashboard')</a></li>
            <li class="nav-item">
              <a class="nav-link text-danger" href="{{ route('logout') }}"
                 onclick="event.preventDefault(); document.getElementById('logout-form-public').submit();">
                @lang('Sign out')
              </a>
              <form id="logout-form-public" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
          @else
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle {{ $is(['volunteer/login','org/login','org/register']) }}"
                 href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
              </a>
              <!-- centered dropdown -->
              <ul class="dropdown-menu dropdown-menu-center shadow-sm" aria-labelledby="loginDropdown">
                <li><a class="dropdown-item" href="{{ url('/org/register') }}">@lang('Organization Register')</a></li>
              
    @include('partials.auth_menu')
  </ul>
            </li>
          @endauth
        </ul>
      </div>
    </div>
  </nav>
</div>
