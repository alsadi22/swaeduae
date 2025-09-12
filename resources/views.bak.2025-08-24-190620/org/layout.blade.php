@php $rtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Organization Console')</title>

  <!-- Argon Dashboard CSS -->
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/nucleo-svg.css') }}">
  <link id="pagestyle" rel="stylesheet" href="{{ asset('vendor/argon/assets/css/argon-dashboard.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/brand.css') }}">

  @stack('head')
  @includeIf('org.partials.branding_styles')
  <style>
    .card { border-radius: 14px; }
    .btn-chip { border-radius: 10px; padding: .5rem .9rem; }
    .kpi .icon { width: 36px; height: 36px; border-radius: 10px; display: grid; place-items: center; }
    .list-clean { list-style: none; padding-left: 0; margin: 0; }
    .list-clean li { display:flex; align-items:center; justify-content:space-between; padding:.4rem 0; border-bottom: 1px solid #f1f2f6; }
    .list-clean li:last-child { border-bottom: 0; }
  </style>
    <style>/* org-sidenav-pin-fix:start */
    /* Shift content so it does not sit under the pinned sidenav (desktop only) */
    @media (min-width: 992px){
      body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left: 280px; }
      [dir="rtl"] body.g-sidenav-show.g-sidenav-pinned .main-content{ margin-left:0; margin-right:280px; }
    }
    /* org-sidenav-pin-fix:end */</style>
</head>
</head>
</head>
<body class="g-sidenav-show g-sidenav-pinned bg-gray-100 {{ $rtl ? 'rtl' : '' }}">
  @includeIf('org.argon._sidenav')

  <main class="main-content position-relative border-radius-lg {{ $rtl ? 'me-3' : 'ms-3' }}">
    @includeIf('admin.argon._navbar')

    <div class="container-fluid py-4">
      @if (session('status'))
        <div class="alert alert-success shadow-sm">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
          <ul class="m-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      @includeIf('org.partials.menu')

      @yield('content')

      @includeIf('admin.argon._footer')
    </div>
  </main>

  <script src="{{ asset('vendor/argon/assets/js/core/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/argon/assets/js/argon-dashboard.min.js') }}"></script>
  <script>(function(){if(!window.Chart){var s=document.createElement('script');s.src='https://cdn.jsdelivr.net/npm/chart.js';s.defer=true;document.head.appendChild(s);}})();</script>
  @stack('scripts')
  <script>
  (function(){
    var KEY="org_sidenav_pinned";
    var body=document.body;
    function setPinned(p){ body.classList.toggle("g-sidenav-pinned", !!p); try{ localStorage.setItem(KEY,p?"1":"0"); }catch(e){} }
    try { setPinned(localStorage.getItem(KEY)!=="0"); } catch(e) {}
    document.addEventListener("click", function(ev){
      var btn = ev.target.closest && ev.target.closest("#org-sidenav-toggle");
      if(!btn) return;
      ev.preventDefault();
      setPinned(!body.classList.contains("g-sidenav-pinned"));
    });
  })();
  </script>
  <script src="{{ asset('js/org-ui.js') }}" defer></script>
</body>
</html>
<!-- org-menu-hotfix:start -->
@push('styles')
<style>
/* Keep the dropdown as a slim fixed panel */
.org-panel {
  position: fixed !important;
  top: 64px !important;              /* adjust if your navbar is taller */
  right: .75rem !important;
  left: auto !important;
  width: min(92vw, 360px) !important;
  max-height: calc(100vh - 88px) !important;
  overflow: auto !important;
  border-radius: 14px !important;
  box-shadow: 0 24px 48px rgba(2,6,23,.24) !important;
  z-index: 1050 !important;
}
[dir="rtl"] .org-panel, [lang="ar"] .org-panel {
  left: .75rem !important; right: auto !important;
}

/* Page backdrop + lock scroll while menu is open */
body.org-panel-open { overflow: hidden !important; }
.org-panel-backdrop {
  position: fixed; inset: 0;
  background: rgba(15,23,42,.35);
  z-index: 1049;
}
</style>
@endpush

@push('scripts')
<script>
(function () {
  if (!window.bootstrap) return;

  // Attach once
  if (window.__orgMenuHotfixAttached) return;
  window.__orgMenuHotfixAttached = true;

  document.addEventListener('shown.bs.dropdown', function (e) {
    // The dropdown wrapper that fired the event (contains the menu)
    var container = e.target;
    var menu = container && container.querySelector('.dropdown-menu');
    if (!menu) return;

    // Make it a fixed side panel
    menu.classList.add('org-panel');

    // Add viewport backdrop + lock scroll
    if (!document.querySelector('.org-panel-backdrop')) {
      var bd = document.createElement('div');
      bd.className = 'org-panel-backdrop';
      document.body.appendChild(bd);
      document.body.classList.add('org-panel-open');

      // Clicking the backdrop closes the dropdown
      bd.addEventListener('click', function () {
        try {
          var inst = bootstrap.Dropdown.getInstance(container.querySelector('[data-bs-toggle="dropdown"]'));
          if (inst) inst.hide();
        } catch (_) {}
      }, { once: true });
    }
  });

  document.addEventListener('hide.bs.dropdown', function () {
    document.body.classList.remove('org-panel-open');
    document.querySelectorAll('.org-panel-backdrop').forEach(function (el) { el.remove(); });
  });
})();
</script>
@endpush
<!-- org-menu-hotfix:end -->
