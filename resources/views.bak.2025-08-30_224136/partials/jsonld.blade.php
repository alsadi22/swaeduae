@once
@php
  $org = [
    "@context" => "https://schema.org",
    "@type"    => "Organization",
    "name"     => config('app.name','SwaedUAE'),
    "url"      => config('app.url','https://swaeduae.ae'),
    "logo"     => config('app.url','https://swaeduae.ae')."/favicon.ico",
    "sameAs"   => [],
    "contactPoint" => [["@type"=>"ContactPoint","contactType"=>"customer support","email"=>"info@swaeduae.ae"]]
  ];
  $website = [
    "@context" => "https://schema.org",
    "@type"    => "WebSite",
    "name"     => config('app.name','SwaedUAE'),
    "url"      => config('app.url','https://swaeduae.ae'),
    "potentialAction" => [
      "@type" => "SearchAction",
      "target" => config('app.url','https://swaeduae.ae')."/search?q={query}",
      "query-input" => "required name=query"
    ]
  ];
@endphp
<script type="application/ld+json">{!! json_encode($org, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($website, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endonce
