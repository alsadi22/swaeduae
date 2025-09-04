@extends("layouts.admin-argon")
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm" style="--bs-bg-opacity:0.9;">
  <div class="container-fluid">
    <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas">
      <i class="bi bi-list"></i>
    </button>

    <span class="navbar-brand ms-2">@yield('page_title','Dashboard')</span>

    <div class="ms-auto d-flex align-items-center gap-3">
      <form class="d-none d-md-block" action="{{ url('/admin/search') }}" method="GET">
        <div class="input-group">
          <input name="q" class="form-control form-control-sm" placeholder="Search...">
          <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
        </div>
      </form>

      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle fs-4 me-2"></i>
          <strong>{{ auth('admin')->user()->email ?? 'Admin' }}</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="{{ url('/admin/profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
          <li><a class="dropdown-item" href="{{ url('/admin/settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('admin.logout', [], false) ?? url('/admin/logout') }}">
              @csrf
              <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
