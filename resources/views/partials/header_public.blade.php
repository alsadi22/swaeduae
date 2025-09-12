<header class="site-header">
  <div class="container nav-row">
    <a href="{{ url('/') }}" class="brand">SwaedUAE</a>
    <button class="nav-toggle" aria-label="Menu" data-nav-toggle>Menu</button>
    <nav class="nav" data-nav>
      <ul>
        <li><a href="{{ url('/opportunities') }}">Opportunities</a></li>
        <li><a href="{{ url('/events') }}">Events</a></li>
        <li><a href="{{ url('/contact') }}">Contact</a></li>
        <li><a href="{{ url('/login') }}">Login</a></li>
        <li><a class="btn btn-primary" href="{{ url('/register') }}">Register</a></li>
      </ul>
    </nav>
  </div>
</header>
