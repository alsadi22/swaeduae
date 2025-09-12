#!/bin/bash
echo "=== Applying full site visual upgrade with Old Lace hero background ==="

# Update app.blade.php layout
cat > resources/views/layouts/app.blade.php <<'BLADE'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sawaed UAE') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fdf5e6;
            font-family: 'Tahoma', sans-serif;
        }
        .navbar-custom {
            background-color: #006d66;
        }
        .navbar-custom .nav-link, .navbar-custom .navbar-brand {
            color: white !important;
        }
        .hero {
            background: #fdf5e6;
            padding: 60px 0;
            text-align: center;
        }
        .hero h1 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.1rem;
            color: #333;
        }
        .event-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Sawaed UAE') }}</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">AR</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">EN</a></li>
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="#">{{ Auth::user()->name }}</a></li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="hero">
        <div class="container">
            <h1>{{ __('اصنع الفرق') }}</h1>
            <p>{{ __('انضم إلى آلاف المتطوعين الذين يساهمون في خدمة 
المجتمع.') }}</p>
            <form class="row justify-content-center mt-4">
                <div class="col-md-6 d-flex">
                    <input type="text" class="form-control" placeholder="{{ __('ابحث عن الفرص...') }}">
                    <button class="btn btn-primary ms-2">{{ __('بحث') }}</button>
                </div>
            </form>
        </div>
    </div>

    <main class="container my-5">
        @yield('content')
    </main>

    <footer class="text-center py-4 bg-light">
        <div class="container">
            &copy; {{ date('Y') }} {{ config('app.name') }} - {{ __('All Rights Reserved') }}
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
BLADE

echo "=== Full site visual upgrade applied successfully ==="

