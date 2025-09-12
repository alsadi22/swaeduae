<header class="site-header">
  <div class="container">
    <a class="logo" href="{{ url("/") }}">SwaedUAE</a>
    <nav class="main-nav">
      <a href="{{ url("/") }}"          class="{{ request()->is("/") ? "active" : "" }}">Home</a>
      <a href="{{ url("/about") }}"     class="{{ request()->is("about") ? "active" : "" }}">About</a>
      <a href="{{ url("/contact") }}"   class="{{ request()->is("contact") ? "active" : "" }}">Contact</a>
      <a href="{{ url("/qr/verify") }}" class="{{ request()->is("qr/verify*") ? "active" : "" }}">QR Verify</a>
    </nav>
  </div>
</header>
