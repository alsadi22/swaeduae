@extends("layouts.admin-argon")
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
  <button id="org-sidenav-toggle" type="button" class="btn btn-sm btn-white d-lg-inline-block me-2" aria-label="Toggle menu">
    <i class="ni ni-menu-36"></i>
  </button>
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <h6 class="font-weight-bolder mb-0">@yield('page_title', __('Dashboard'))</h6>
    </nav>
  </div>
</nav>
@include("components.lang-toggle")
