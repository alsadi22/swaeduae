@auth
  @if(!auth()->user()->hasVerifiedEmail())
    <div class="alert alert-warning mb-0 rounded-0">
      {{ __('Your email is not verified.') }}
      <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
        @csrf
        <button class="btn btn-sm btn-outline-dark ms-2" type="submit">{{ __('Resend link') }}</button>
      </form>
      <a class="btn btn-sm btn-link" href="{{ route('verification.notice') }}">{{ __('More info') }}</a>
    </div>
  @endif
@endauth
