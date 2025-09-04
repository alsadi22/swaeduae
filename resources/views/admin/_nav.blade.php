@php $is = fn($name)=> Route::currentRouteNamed($name.'*'); @endphp
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container container-narrow">
    <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#anav"><span class="navbar-toggler-icon"></span></button>
    <div id="anav" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        @if(Route::has('admin.users.index')) <li class="nav-item"><a class="nav-link {{ $is('admin.users')?'active':'' }}" href="{{ route('admin.users.index') }}">Users</a></li>@endif
        @if(Route::has('admin.organizations.index')) <li class="nav-item"><a class="nav-link {{ $is('admin.organizations')?'active':'' }}" href="{{ route('admin.organizations.index') }}">Organizations</a></li>@endif
        @if(Route::has('admin.opportunities.index')) <li class="nav-item"><a class="nav-link {{ $is('admin.opportunities')?'active':'' }}" href="{{ route('admin.opportunities.index') }}">Opportunities</a></li>@endif
        @if(Route::has('admin.reports.index')) <li class="nav-item"><a class="nav-link {{ $is('admin.reports')?'active':'' }}" href="{{ route('admin.reports.index') }}">Reports</a></li>@endif
        @if(Route::has('admin.settings.index')) <li class="nav-item"><a class="nav-link {{ $is('admin.settings')?'active':'' }}" href="{{ route('admin.settings.index') }}">Settings</a></li>@endif
      </ul>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ url('/') }}" target="_blank">Public Site</a>
        <form method="POST" action="{{ route('logout.perform') }}">@csrf
          <button class="btn btn-danger btn-sm" type="submit">Sign out</button>
        </form>
      </div>
    </div>
  </div>
</nav>
