<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>@yield('title','Admin') · SwaedUAE</title>

  <link rel="icon" href="/img/icon-192.png">
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --sidebar-w: 260px;
      --brand: #5e72e4; /* Argon primary */
      --brand-2:#11cdef; /* info */
      --brand-3:#f5365c; /* danger */
      --brand-4:#2dce89; /* success */
      --bg:#f6f8fb;
    }
    html,body{height:100%}
    body{font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);}

    .layout{display:flex;min-height:100vh}
    .sidebar{
      width:var(--sidebar-w); background:#fff; border-right:1px solid #eef0f3; position:sticky; top:0; height:100vh; overflow:auto;
    }
    .brand{
      background:linear-gradient(135deg,var(--brand),#825ee4);
      color:#fff; padding:18px 18px; font-weight:700; letter-spacing:.2px;
    }
    .brand small{opacity:.9; font-weight:600}
    .side-link{display:block;padding:10px 16px;color:#5f6b7a;text-decoration:none;border-left:3px solid transparent}
    .side-link:hover{background:#f4f6fb}
    .side-link.active{background:#eef2ff;color:#1e2a78;border-left-color:var(--brand);font-weight:700}

    .content{flex:1; display:flex; flex-direction:column}
    .topbar{background:#fff;border-bottom:1px solid #eef0f3;padding:10px 16px;display:flex;gap:12px;align-items:center}
    .search{flex:1}
    .search input{border-radius:10px}

    .container-narrow{max-width:1200px}

    .card-kpi{
      border:0; border-radius:16px; color:#fff; overflow:hidden;
      box-shadow:0 8px 24px rgba(50,50,93,.1),0 3px 6px rgba(0,0,0,.08);
    }
    .kpi-1{background:linear-gradient(135deg,var(--brand),#6a82fb)}
    .kpi-2{background:linear-gradient(135deg,#00b8d8,var(--brand-2))}
    .kpi-3{background:linear-gradient(135deg,#ff5b7e,var(--brand-3))}
    .kpi-4{background:linear-gradient(135deg,#25d366,var(--brand-4))}
    .card-kpi .num{font-size:34px;font-weight:800;line-height:1}
    .card-kpi .lbl{opacity:.95;font-weight:600}
    .card-kpi .small{opacity:.9}

    footer{color:#98a2b3}
    @media (max-width: 991px){
      .sidebar{position:fixed; z-index:1040; left:-100%; transition:left .25s}
      .sidebar.show{left:0}
      .content{margin-left:0}
    }
  </style>
  @stack('styles')
</head>
<body>
  <div class="layout">
    {{-- SIDEBAR --}}
    @includeIf('admin._sidebar')

    {{-- MAIN CONTENT --}}
    <div class="content">
      {{-- TOPBAR --}}
      @includeIf('admin._topbar')

      <main class="py-4">
        <div class="container container-narrow">
          @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
          @if($errors->any())
            <div class="alert alert-danger"><b>There were problems:</b><ul class="mb-0">
              @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul></div>
          @endif

          @yield('content')
        </div>
      </main>

      <footer class="text-center py-4 small">SwaedUAE Admin</footer>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <script>
    // mobile sidebar toggle
    document.addEventListener('click', e=>{
      if(e.target.closest('[data-toggle="sidebar"]')){
        document.querySelector('.sidebar')?.classList.toggle('show');
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
