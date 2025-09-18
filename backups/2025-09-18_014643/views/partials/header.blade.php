<header class="site-header">
  <div class="container">
    <a class="logo" href="{{ url("/") }}">SwaedUAE</a>

    <button class="nav-toggle" aria-label="Toggle menu" aria-expanded="false">☰</button>

    <nav class="main-nav" data-nav>
      <a href="{{ url("/") }}"                class="{{ request()->is("/") ? "active" : "" }}">Home</a>
      <a href="/opportunities"/opportunities") }}"   class="{{ request()->is("opportunities") ? "active" : "" }}">Opportunities</a>
      <a href="/about"/about") }}"           class="{{ request()->is("about") ? "active" : "" }}">About</a>
      <a href="/contact"/contact") }}"         class="{{ request()->is("contact") ? "active" : "" }}">Contact</a>
      <a href="{{ url("/qr/verify") }}"       class="{{ request()->is("qr/verify*") ? "active" : "" }}">QR Verify</a>
    </nav>

    <div class="a11y">
      <button class="btn-link" data-contrast title="Contrast">⃞/■</button>
      <button class="btn-link" data-fontminus title="A-">A−</button>
      <button class="btn-link" data-fontplus  title="A+">A+</button>
    </div>
  </div>
</header>

<!-- Hard-link a[data-nav] even if other JS cancels the click (capture phase) -->
<script id="auth-nav-capture">
document.addEventListener('click', function(e){
  var a = e.target && e.target.closest ? e.target.closest('a[data-nav]') : null;
  if (!a) return;
  // Navigate immediately; capture-phase runs before any preventDefault on bubble/target
  try { window.location.assign(a.getAttribute('href')); } catch(_) {}
}, true); // <-- CAPTURE PHASE
</script>
