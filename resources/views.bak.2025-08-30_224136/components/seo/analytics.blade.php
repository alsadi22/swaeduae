@php(
  $driver = config('analytics.driver') ?? env('ANALYTICS_DRIVER')
)
@php(
  $domain = config('analytics.plausible_domain') ?? env('PLAUSIBLE_DOMAIN') ?? parse_url(config('app.url', 'https://swaeduae.ae'), PHP_URL_HOST)
)
@if (($driver === 'plausible') || (env('PLAUSIBLE_ENABLED') && $domain))
  <script defer data-domain="{{ $domain }}" src="https://plausible.io/js/script.js"></script>
@endif
