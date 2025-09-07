<header class="site-header">
  <div class="container" style="display:flex;justify-content:space-between;align-items:center;min-height:56px;">
    <a class="logo" href="{{ url('/') }}">SwaedUAE</a>
    <ul class="nav">
      <li><a href="{{ url('/opportunities') }}">{{ __('Opportunities') }}</a></li>
      <li><a href="{{ url('/events') }}">{{ __('Events') }}</a></li>
      <li><a href="{{ url('/contact') }}">{{ __('Contact') }}</a></li>

      @guest
        <li><a href="{{ route('login') }}">{{ __('Login') }}</a></li>
        <li><a href="{{ route('register') }}">{{ __('Register') }}</a></li>
      @endguest

      @auth
        @can('admin-access')
          <li><a href="{{ url('/admin') }}">Admin</a></li>
        @endcan
        @if(method_exists(auth()->user(),'hasRole') && auth()->user()->hasRole('org'))
          <li><a href="{{ url('/org') }}">Org Dashboard</a></li>
        @endif
        <li><a href="{{ url('/my/profile') }}">{{ __('My Profile') }}</a></li>
        <li>
          <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf
            <button class="btn" type="submit">{{ __('Logout') }}</button>
          </form>
        </li>
      @endauth

      <li><a href="{{ url('lang/'.(app()->getLocale()==='ar'?'en':'ar')) }}">{{ app()->getLocale()==='ar'?'EN':'AR' }}</a></li>
    </ul>
  </div>
</header>
