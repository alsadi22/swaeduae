<header class="site-header">
  <div class="container nav-row">
    <a href="{{ url('/') }}" class="brand">SwaedUAE</a>
    <button class="nav-toggle" aria-label="Menu" data-nav-toggle>Menu</button>
    <nav class="nav" data-nav>
      <ul>
        <li><a href="{{ url('/opportunities') }}" class="{{ request()->is('opportunities*') ? 'active' : '' }}">Opportunities</a></li>
        <li><a href="{{ url('/events') }}" class="{{ request()->is('events*') ? 'active' : '' }}">Events</a></li>
        <li><a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Contact</a></li>
        <li><a href="{{ url('/login') }}">Login</a></li>
        <li><a class="btn btn-primary" href="{{ url('/register') }}">Register</a></li>
      </ul>
    </nav>
  </div>
</header>
