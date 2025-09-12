@auth
  <div class="dropdown">
    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Account</a>
    <div class="dropdown-menu dropdown-menu-end">
      <a class="dropdown-item" href="{{ url('/my/profile') }}">My Profile</a>
      <form method="POST" action="{{ route('logout.perform') }}" class="m-0 p-0">
        @csrf
        <button type="submit" class="dropdown-item">Logout</button>
      </form>
    </div>
  </div>
@else
  <a href="{{ url('/login') }}">Login</a>
@endauth
