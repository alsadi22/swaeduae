@php $__orgAuth = request()->is('org/login') || request()->is('org/register'); @endphp
<footer class="container py-4 text-center small">
  <a href="{{ route('about') }}">{{ __('About') }}</a> ·
  <a href="{{ route('faq') }}">{{ __('FAQ') }}</a> ·
  <a href="{{ url('/privacy') }}">{{ __('Privacy') }}</a> ·
  <a href="{{ url('/terms') }}">{{ __('Terms') }}</a> ·
  @if (Route::has('contact.get'))
    <a href="{{ route('contact.get') }}">{{ __('Contact') }}</a>
  @endif
</footer>
@endif {{-- ORG_AUTH_GUARD --}}
