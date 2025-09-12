@if(isset($opportunity) && ($opportunity->id ?? null))
  <a class="btn btn-sm btn-outline-primary"
     href="{{ route('org.certificates.index', ['opportunity' => $opportunity->id]) }}">
    {{ __('Certificates') }}
  </a>
@endif
