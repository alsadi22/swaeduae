@guest
<li class="nav-item dropdown">
    <a class="btn btn-primary dropdown-toggle px-3" href="#" id="authMenu" role="button"
       data-bs-toggle="dropdown" aria-expanded="false">
        {{ __('Login / Register') }}
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="authMenu">
        <li><a class="dropdown-item" href="{{ route('login',[ 'type'=>'volunteer' ]) }}">{{ __('Volunteer Login') }}</a></li>
        <li><a class="dropdown-item" href="{{ url('/org/login') }}">{{ __('Organization Login') }}</a></li>
        <li><a class="dropdown-item" href="{{ url('/org/register') }}">{{ __('Organization Register') }}</a></li>
    </ul>
</li>
@endguest
