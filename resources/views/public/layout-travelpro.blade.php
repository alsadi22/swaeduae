<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('service-worker.js') }}');
            });
        }
    </script>
    @unless(app()->runningUnitTests())
        {{-- @vite(['resources/css/app.css','resources/js/app.js']) --}}
    @endunless
</head>
<body>
    @include('partials.header', ['isPublic' => true])
    <main>
        @yield('content')
    </main>
    @includeIf('partials.footer-public')
</body>
</html>
