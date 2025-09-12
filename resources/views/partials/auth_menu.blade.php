<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="authMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    Sign In
  </a>
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authMenu">
    <li><a class="dropdown-item" href="{{ url('/login') }}">Volunteer Sign In</a></li>
    <li><a class="dropdown-item" href="{{ url('/register') }}">Volunteer Register</a></li>
    <li><hr class="dropdown-divider"></li>
    <li><a class="dropdown-item" href="{{ url('/org/login') }}">Organization Sign In</a></li>
    <li><a class="dropdown-item" href="{{ url('/admin/login') }}">Admin</a></li>
  </ul>
</li>
