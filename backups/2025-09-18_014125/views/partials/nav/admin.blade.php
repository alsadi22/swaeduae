<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-3">
  <a class="navbar-brand" href="{{ route('admin.root') }}">Swaed Admin</a>
  <ul class="navbar-nav ms-auto">
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.root') }}">Dashboard</a></li>
    <li class="nav-item"></li>
  </ul>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
</nav>
