@php $u = auth()->user(); @endphp
@auth
    <li class="nav-item">
        <a class="nav-link" href="{{ route('volunteer.dashboard') }}">{{ __('My Dashboard') }}</a>
    </li>

    @if(($u->is_admin ?? false) || (method_exists($u, 'hasRole') && $u->hasRole('admin')))
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/admin') }}">{{ __('Admin') }}</a>
        </li>
    @endif

    <li class="nav-item">
        <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf
            <button class="btn btn-link nav-link p-0" type="submit">{{ __('Sign out') }}</button>
        </form>
    </li>
@else
    <li class="nav-item">
    @include('partials.auth_dropdown') {{-- auth dropdown --}}
    </li>
@endauth
{{-- marker nav_links.blade.php 04:04:24 --}}
