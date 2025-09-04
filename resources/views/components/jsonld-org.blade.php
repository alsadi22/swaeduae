@php
  $orgJson = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => config('app.name','SwaedUAE'),
    'url'      => url('/'),
    'logo'     => url('/favicon.ico'),
  ];
@endphp
<script type="application/ld+json">
{!! json_encode($orgJson, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) !!}
</script>
