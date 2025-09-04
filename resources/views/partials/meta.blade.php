<title>@yield('title', 'SwaedUAE')</title>
<meta name="description" content="@yield('meta_description', 'Volunteer opportunities and community projects in the UAE.')">
<link rel="icon" href="{{ asset('images/favicon.ico') }}">
<link rel="manifest" href="/manifest.json">
<meta property="og:title" content="@yield('og_title', 'SwaedUAE')" />
<meta property="og:description" content="@yield('og_desc', 'Volunteer opportunities and community projects in the UAE.')" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:image" content="{{ asset('images/og.jpg') }}" />
<meta name="twitter:card" content="summary_large_image" />
