<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('Applying...') }}</title>
</head>
<body>
  <p style="font-family:system-ui, -apple-system, Segoe UI, Roboto">{{ __('Submitting your application...') }}</p>
  <form id="autoPost" method="POST" action="{{ route('opportunities.apply', $id) }}">
    @csrf
    <noscript>
      <button type="submit">{{ __('Click to confirm apply') }}</button>
    </noscript>
  </form>
  <script>
    (function(){ try{ document.getElementById('autoPost').submit(); }catch(e){} })();
  </script>
</body>
</html>
