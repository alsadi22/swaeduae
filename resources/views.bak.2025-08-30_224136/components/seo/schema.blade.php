@php($orgName = config('app.name'))
@php($base = rtrim(config('app.url','https://swaeduae.ae'),'/'))
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'Organization',
  'name'     => $orgName,
  'url'      => $base,
  'logo'     => $base.'/favicon.ico',
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@type'    => 'WebSite',
  'name'     => $orgName,
  'url'      => $base,
  'potentialAction' => [
    '@type'       => 'SearchAction',
    'target'      => 'https://www.google.com/search?q=site%3Aswaeduae.ae+{search_term_string}',
    'query-input' => 'required name=search_term_string',
  ],
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
