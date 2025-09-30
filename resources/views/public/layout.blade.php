<!doctype html>
<html lang="{{ str_replace("_","-",app()->getLocale()) }}" dir="{{ app()->getLocale()==="ar" ? "rtl" : "ltr" }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield("title","SwaedUAE")</title>

  <!-- Theme CSS -->
  <link href="/assets/app.css?v={{ file_exists(public_path("assets/app.css")) ? substr(md5_file(public_path("assets/app.css")),0,8) : time() }}" rel="stylesheet">
</head>
<body class="public-site">
  <a class="skip-link" href="#main">Skip to content</a>
  @includeIf("partials.header")

  <main id="main">@yield("content")</main>

  @includeIf("partials.footer")

  <!-- Minimal JS (a11y + mobile nav + persist) -->
  <script src="/assets/app.js?v={{ file_exists(public_path("assets/app.js")) ? substr(md5_file(public_path("assets/app.js")),0,8) : time() }}"></script>
</body>
</html>
