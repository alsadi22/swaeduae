<nav class="navbar navbar-expand-lg navbar-light glass-nav">
  <div class="container">
    <!-- Brand (left) -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
      @php $logo = $appSettings['logo'] ?? null; @endphp
      @if($logo)<img src="{{ asset('storage/'.$logo) }}" alt="Logo" style="height:28px" class="rounded">@endif
      <span>{{ $appSettings['site_name'] ?? 'SawaedUAE' }}</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav (center) + Auth (right) -->
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('opportunities*') ? 'active' : '' }}" href="{{ url('/opportunities') }}">Opportunities</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('events*') ? 'active' : '' }}" href="{{ url('/events') }}">Events</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('organizations*') ? 'active' : '' }}" href="{{ url('/organizations') }}">Organizations</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->is('gallery') ? 'active' : '' }}" href="{{ url('/gallery') }}">Gallery</a></li>
        {{-- AUTH-MENU-INJECT START --}}
        @auth
            @includeIf('partials.account_menu')
        @else
            @includeIf('partials.auth_menu')
        @endauth
        {{-- AUTH-MENU-INJECT END --}}
      </ul>

      <div class="d-flex align-items-center gap-2">
        @if(auth()->check())
          <a class="btn btn-outline-secondary btn-sm" href="{{ route('profile') }}">Dashboard</a>
          
        @else
          <a class="btn btn-outline-primary btn-sm" href="{{ route('login',['type'=>'volunteer']) }}">Login</a>
          <a class="btn btn-primary btn-sm" href="{{ url('/register') }}">Register</a>
        @endif
      </div>
    </div>
  </div>
</nav>

<script>
(function(){
  const nav = document.querySelector('.glass-nav');
  if(!nav) return;
  const hero = document.querySelector('.hero');
  function onScroll(){
    const atTop = hero ? window.scrollY < 20 : false;
    if(atTop){ nav.classList.add('transparent'); nav.classList.remove('scrolled'); }
    else{ nav.classList.add('scrolled'); nav.classList.remove('transparent'); }
  }
  onScroll();
  window.addEventListener('scroll', onScroll, {passive:true});
})();
</script>
