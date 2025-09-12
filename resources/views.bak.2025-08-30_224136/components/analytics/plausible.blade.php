@if (config('app.env') === 'production' && filter_var(env('PLAUSIBLE_ENABLED', false), FILTER_VALIDATE_BOOLEAN))
  <script defer data-domain="{{ env('PLAUSIBLE_DOMAIN','swaeduae.ae') }}" src="https://plausible.io/js/script.js"></script>
@endif

