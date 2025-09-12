<div class="container" style="max-width:1140px">
  <div class="d-flex justify-content-end gap-2 py-1 small">
    @guest
      <a href="{{ url('/login') }}">Sign in</a>
    @else
      <a href="{{ url('/account/profile') }}">My Account</a>
      <form method="POST" action="{{ url('/logout') }}" class="d-inline">@csrf
        <button class="btn btn-sm btn-link p-0 align-baseline">Sign out</button>
      </form>
    @endguest
  </div>
</div>
