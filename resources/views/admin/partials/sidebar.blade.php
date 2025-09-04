@extends("layouts.admin-argon")
<aside class="admin-sidebar d-none d-md-flex flex-column p-3 bg-white border-end" style="min-height:100vh; width:260px;">
  <a href="{{ url('/admin') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
    <span class="fs-5 fw-bold">SwaedUAE Admin</span>
  </a>
  <hr>
  <ul class="nav nav-pills flex-column mb-auto gap-1">
    <li class="nav-item"><a class="nav-link {{ request()->is('admin') ? 'active' : '' }}" href="{{ url('/admin') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
    <li><a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="{{ url('/admin/users') }}"><i class="bi bi-people me-2"></i>Users</a></li>
    <li><a class="nav-link {{ request()->is('admin/orgs*') ? 'active' : '' }}" href="{{ url('/admin/orgs') }}"><i class="bi bi-building me-2"></i>Organizations</a></li>
    <li><a class="nav-link {{ request()->is('admin/opps*') || request()->is('admin/opportunities*') ? 'active' : '' }}" href="{{ url('/admin/opps') }}"><i class="bi bi-briefcase me-2"></i>Opportunities</a></li>
    <li><a class="nav-link {{ request()->is('admin/events*') ? 'active' : '' }}" href="{{ url('/admin/events') }}"><i class="bi bi-calendar-event me-2"></i>Events</a></li>
    <li><a class="nav-link {{ request()->is('admin/applicants*') ? 'active' : '' }}" href="{{ url('/admin/applicants') }}"><i class="bi bi-person-check me-2"></i>Applicants</a></li>
    <li><a class="nav-link {{ request()->is('admin/attendance*') ? 'active' : '' }}" href="{{ url('/admin/attendance') }}"><i class="bi bi-qr-code-scan me-2"></i>Attendance</a></li>
    <li><a class="nav-link {{ request()->is('admin/certificates*') ? 'active' : '' }}" href="{{ url('/admin/certificates') }}"><i class="bi bi-award me-2"></i>Certificates</a></li>
    <li><a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" href="{{ url('/admin/reports') }}"><i class="bi bi-graph-up me-2"></i>Reports</a></li>
    <li><a class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}" href="{{ url('/admin/categories') }}"><i class="bi bi-tags me-2"></i>Categories</a></li>
    <li><a class="nav-link {{ request()->is('admin/partners*') ? 'active' : '' }}" href="{{ url('/admin/partners') }}"><i class="bi bi-handshake me-2"></i>Partners</a></li>
    <li><a class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}" href="{{ url('/admin/settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
  </ul>
  <hr>
  <form method="POST" action="{{ route('admin.logout', [], false) ?? url('/admin/logout') }}">
    @csrf
    <button class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-1"></i> Logout</button>
  </form>
</aside>
