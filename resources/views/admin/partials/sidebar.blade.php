<aside class="admin-sidebar d-none d-md-flex flex-column p-3 bg-white border-end" style="min-height:100vh;">
  <a href="{{ url('/admin') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
    <span class="fs-5 fw-bold">SwaedUAE Admin</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto gap-1">
    <li class="nav-item"><a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" href="{{ url('/admin') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
    <li><a class="nav-link {{ request()->is('admin/hours*') ? 'active' : '' }}" href="{{ route('admin.hours.index') }}"><i class="bi bi-clock-history me-2"></i>Hours</a></li>
    <li><a class="nav-link {{ request()->is('admin/certificates*') ? 'active' : '' }}" href="{{ route('admin.certificates.index') }}"><i class="bi bi-award me-2"></i>Certificates</a></li>
  </ul>
</aside>
