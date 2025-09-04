@php
  $driver = config('analytics.driver');
  $domain = config('analytics.plausible_domain');
@endphp
@if ($driver === 'plausible' && $domain)
  <script defer data-domain="{{ $domain }}" src="https://plausible.io/js/script.js"></script>
@endif
