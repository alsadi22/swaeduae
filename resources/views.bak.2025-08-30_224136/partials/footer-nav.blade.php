@php $__orgAuth = request()->is('org/login') || request()->is('org/register'); @endphp
<footer class="container py-4 text-center small">
  <a href="{{ route('about') }}">{{ __('About') }}</a> ·
  <a href="{{ route('faq') }}">{{ __('FAQ') }}</a> ·
  <a href="{{ route('pages.privacy') }}">{{ __('Privacy') }}</a> ·
  <a href="{{ route('pages.terms') }}">{{ __('Terms') }}</a> ·
  <a href="{{ route('contact.show') }}">{{ __('Contact') }}</a>
</footer>
@endif {{-- ORG_AUTH_GUARD --}}
