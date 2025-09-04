@php $is = fn($name)=> Route::currentRouteNamed($name.'*'); @endphp
<aside class="sidebar">
  <div class="brand"><div>SwaedUAE</div><small>Admin Panel</small></div>
  <nav class="py-2">
    <a class="side-link {{ $is('admin.dashboard')?'active':'' }}" href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
    @if(Route::has('admin.users.index'))<a class="side-link {{ $is('admin.users')?'active':'' }}" href="{{ route('admin.users.index') }}">👥 Users</a>@endif
    @if(Route::has('admin.organizations.index'))<a class="side-link {{ $is('admin.organizations')?'active':'' }}" href="{{ route('admin.organizations.index') }}">🏢 Organizations</a>@endif
    @if(Route::has('admin.opportunities.index'))<a class="side-link {{ $is('admin.opportunities')?'active':'' }}" href="{{ route('admin.opportunities.index') }}">📌 Opportunities</a>@endif
    @if(Route::has('admin.applicants.index'))<a class="side-link {{ $is('admin.applicants')?'active':'' }}" href="{{ route('admin.applicants.index') }}">✅ Applicants</a>@endif
    @if(Route::has('admin.integrations.index'))<a class="side-link {{ $is('admin.integrations')?'active':'' }}" href="{{ route('admin.integrations.index') }}">🔌 Integrations</a>@endif
    @if(Route::has('admin.reports.index'))<a class="side-link {{ $is('admin.reports')?'active':'' }}" href="{{ route('admin.reports.index') }}">📊 Reports</a>@endif
    @if(Route::has('admin.settings.index'))<a class="side-link {{ $is('admin.settings')?'active':'' }}" href="{{ route('admin.settings.index') }}">⚙️ Settings</a>@endif
    <a class="side-link" href="{{ url('/') }}" target="_blank">🌐 Public Site</a>
  </nav>
</aside>
