<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" dir="{{ app()->getLocale()==='ar'?'rtl':'ltr' }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Org | SwaedUAE')</title>
  @stack('head')
</head>
<body>
  <header class="site-header">
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;min-height:56px;">
      <a class="logo" href="{{ url('/') }}">SwaedUAE</a>
      <nav>
        <ul class="nav" style="list-style:none;display:flex;gap:16px;margin:0;padding:0">
          <li><a href="{{ url('/org/dashboard') }}">{{ __('Dashboard') }}</a></li>
          <li><a href="{{ url('/org/opportunities/create') }}">{{ __('New Opportunity') }}</a></li>
          <li><a href="{{ url('/org/settings') }}">{{ __('Settings') }}</a></li>
          <li>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf
              <button class="btn" type="submit">{{ __('Logout') }}</button>
            </form>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="container">
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>
