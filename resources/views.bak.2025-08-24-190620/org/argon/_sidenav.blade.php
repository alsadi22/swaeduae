<aside id="sidenav-main" class="sidenav navbar navbar-vertical navbar-expand-xs border-0 bg-white fixed-end" data-color="primary">
  <div class="sidenav-header text-center py-3">
    <span class="h6 mb-0">Organization Console</span>
  </div>

  <hr class="horizontal dark mt-0 mb-2">

  <div class="collapse navbar-collapse  w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      <li class="nav-item">
        <a class="nav-link {{ request()->is('org') || request()->is('org/dashboard') ? 'active' : '' }}" href="{{ url('/org') }}">
          <span class="nav-link-text">Dashboard</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('org/events*') ? 'active' : '' }}" href="{{ url('/org/events') }}">
          <span class="nav-link-text">Events</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('org/reports*') ? 'active' : '' }}" href="{{ url('/org/reports') }}">
          <span class="nav-link-text">Reports</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('org/profile') ? 'active' : '' }}" href="{{ url('/org/profile') }}">
          <span class="nav-link-text">Profile</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link {{ request()->is('org/license*') ? 'active' : '' }}" href="{{ url('/org/license/request') }}">
          <span class="nav-link-text">License Request</span>
        </a>
      </li>

      <hr class="horizontal dark my-2">
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/') }}"><span class="nav-link-text">View Site</span></a>
      </li>
      <li class="nav-item">
        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
          @csrf
          <button class="nav-link btn btn-link p-0"><span class="nav-link-text">Sign out</span></button>
        </form>
      </li>
    </ul>
  </div>
</aside>
