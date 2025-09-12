<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start" id="sidenav-main">
  <div class="sidenav-header">
    <a class="navbar-brand m-0" href="{{ url('/org') }}">Organization</a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link {{ request()->is('org') || request()->is('org/dashboard') ? 'active' : '' }}" href="{{ url('/org') }}">
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->is('org/events*') ? 'active' : '' }}" href="{{ url('/org/events') }}">
          <span class="nav-link-text ms-1">Opportunities</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->is('org/reports*') ? 'active' : '' }}" href="{{ url('/org/reports') }}">
          <span class="nav-link-text ms-1">Reports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->is('org/license/request') ? 'active' : '' }}" href="{{ url('/org/license/request') }}">
          <span class="nav-link-text ms-1">License Request</span>
        </a>
      </li>
    </ul>
  </div>
</aside>
