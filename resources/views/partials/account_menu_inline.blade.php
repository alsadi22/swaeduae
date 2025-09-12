@php $user = Illuminate\Support\Facades\Auth::user(); @endphp
<div class="dropdown">
  <a class="link-secondary dropdown-toggle" href="#" id="accountMenu" data-bs-toggle="dropdown" aria-expanded="false">
    {{ $user->name ?? 'Account' }}
  </a>
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountMenu">
    <li><a class="dropdown-item" href="{{ url('/my/profile') }}">My Profile</a></li>
    <li><a class="dropdown-item" href="{{ url('/my/certificates') }}">My Certificates</a></li>
    <li><a class="dropdown-item" href="{{ url('/org') }}">Org Dashboard</a></li>
    <li><a class="dropdown-item" href="{{ url('/admin') }}">Admin</a></li>
    <li><hr class="dropdown-divider"></li>
    <li>
      <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">@csrf
        <button type="submit" class="btn btn-link p-0">Logout</button>
      </form>
    </li>
  </ul>
</div>
