<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'SawaedUAE') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav>
        <a href="{{ route('home') }}">Home</a>
        <!-- Other nav links -->

        <!-- Language Switch -->
        <span style="float:right;">
            <a href="{{ route('lang.switch', 'ar') }}">العربية</a> |
            <a href="{{ route('lang.switch', 'en') }}">English</a>
        </span>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} SawaedUAE
    </footer>
</body>
</html>
