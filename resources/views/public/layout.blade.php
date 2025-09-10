<html lang="{{ str_replace("_","-",app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield("title","SwaedUAE")</title>
  <link rel="stylesheet" href="{{ asset("assets/travelpro.min.css") }}">
  @yield("head")
</head>
<body>
  @includeFirst(["partials.header_public","partials.header"])
  <div class="container">@yield("content")</div>
  @includeIf("partials.footer")
  @yield("scripts")
</body>
</html>
