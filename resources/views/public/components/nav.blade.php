<header class="border-b border-slate-200 bg-white/80 backdrop-blur">
  <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
    <a href="/" class="flex items-center gap-2 font-semibold">
      <span class="text-sky-600">SwaedUAE</span>
    </a>
    <nav class="hidden md:flex items-center gap-6 text-[15px]">
      <a href="/about" class="hover:text-sky-600">About</a>
      <a href="/opportunities" class="hover:text-sky-600">Opportunities</a>
      <!-- Stories link temporarily removed until route exists -->
      <a href="/organizations" class="hover:text-sky-600">Organizations</a>
      <a href="/certificates/verify" class="hover:text-sky-600">Verify Certificate</a>
    </nav>
    <div class="hidden md:flex items-center gap-2">
      <a href="/login" class="btn">Login</a>
      <a href="/register" class="btn btn-brand">Sign up</a>
    </div>
    <button class="md:hidden" id="navToggle" aria-label="Menu">
      <svg width="28" height="28" viewBox="0 0 24 24"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
  </div>
  <div id="mobileNav" class="md:hidden hidden border-t border-slate-200">
    <div class="px-4 py-3 grid gap-3">
      <a href="/about">About</a>
      <a href="/opportunities">Opportunities</a>
      <!-- Stories link temporarily removed until route exists -->
      <a href="/organizations">Organizations</a>
      <a href="/certificates/verify">Verify Certificate</a>
      <div class="flex gap-2 pt-2">
        <a href="/login" class="btn grow">Login</a>
        <a href="/register" class="btn btn-brand grow">Sign up</a>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded',()=> {
      const t=document.getElementById('navToggle'), m=document.getElementById('mobileNav');
      if(t && m){ t.onclick=()=> m.classList.toggle('hidden'); }
    });
  </script>
</header>
