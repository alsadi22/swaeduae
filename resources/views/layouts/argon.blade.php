<!doctype html><html lang="{{ app()->getLocale() }}"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title ?? config('app.name') }}</title>
<link rel="stylesheet" href="{{ asset('vendor/argon/assets/css/argon.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/argon/css/argon.css') }}">
</head><body class="min-h-screen antialiased bg-gray-50">
@yield('content')
<script src="{{ asset('vendor/argon/assets/js/argon.js') }}"></script>
<script src="{{ asset('vendor/argon/js/argon.js') }}"></script>
</body></html>
