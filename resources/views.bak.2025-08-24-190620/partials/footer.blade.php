@php $__orgAuth = request()->is('org/login') || request()->is('org/register'); @endphp
<footer class="py-5 mt-auto border-top bg-light">
  <div class="container">
    <div class="row g-4">
      <div class="col-12 col-md-4">
        <h6 class="text-uppercase text-muted">{{ __('About') }}</h6>
        <p class="mb-2">{{ __('SawaedUAE helps volunteers discover opportunities, track hours, and earn verified certificates.') }}</p>
        <div class="small text-muted">&copy; {{ date('Y') }} SawaedUAE</div>
      </div>

      <div class="col-6 col-md-2">
        <h6 class="text-uppercase text-muted">{{ __('Explore') }}</h6>
        <ul class="list-unstyled mb-0">
          <li><a class="link-dark text-decoration-none" href="{{ url('/opportunities') }}">{{ __('Opportunities') }}</a></li>
          <li><a class="link-dark text-decoration-none" href="{{ url('/events') }}">{{ __('Events') }}</a></li>
          <li><a class="link-dark text-decoration-none" href="{{ url('/gallery') }}">{{ __('Gallery') }}</a></li>
          <li><a class="link-dark text-decoration-none" href="{{ url('/verify/EXAMPLE') }}">{{ __('Verify a certificate') }}</a></li>
        </ul>
      </div>

      <div class="col-6 col-md-3">
        <h6 class="text-uppercase text-muted">{{ __('For volunteers') }}</h6>
        <ul class="list-unstyled mb-0">
          <li><a class="link-dark text-decoration-none" href="{{ route('my.certificates') }}">{{ __('My Certificates') }}</a></li>
          <li><a class="link-dark text-decoration-none" href="{{ request()->is('org*') ? route('org.dashboard') : route('profile') }}">{{ __('My Dashboard') }}</a></li>
        </ul>
      </div>

      <div class="col-12 col-md-3">
        <h6 class="text-uppercase text-muted">{{ __('Contact') }}</h6>
        <ul class="list-unstyled small mb-2">
          <li><a class="link-dark text-decoration-none" href="mailto:info@swaeduae.ae">info@swaeduae.ae</a></li>
          <li><span class="text-muted">{{ __('United Arab Emirates') }}</span></li>
        </ul>
        <div class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-secondary" href="#" aria-label="X/Twitter">X</a>
          <a class="btn btn-sm btn-outline-secondary" href="#" aria-label="Facebook">Fb</a>
          <a class="btn btn-sm btn-outline-secondary" href="#" aria-label="Instagram">Ig</a>
        </div>
      </div>
    </div>
  </div>
</footer>
@endif {{-- ORG_AUTH_GUARD --}}
