<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="Sawaed UAE Volunteer Platform" />
    <meta property="og:title" content="@yield('title', config('app.name'))" />
    <meta property="og:description" content="Join Sawaed UAE and contribute your time to make a difference." />
    <meta property="og:type" content="website" />
    <link rel="canonical" href="{{ url()->current() }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>html { scroll-behavior: smooth; } [tabindex="0"]:focus { outline: 2px solid #1e3a8a; }</style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <nav class="bg-white border-b shadow p-3 flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-extrabold text-blue-900 text-xl" aria-label="Home">
            <img src="{{ asset('images/logo.png') }}" alt="SawaedUAE Logo" class="w-8 h-8" /> SawaedUAE
        </a>
        <div>
            <a href="{{ route('login') }}" class="text-blue-700 font-bold mr-4">Login</a>
            <a href="{{ route('register') }}" class="bg-blue-700 text-white px-4 py-2 rounded-lg font-bold">Sign Up</a>
            <button onclick="document.getElementById('langForm').submit()" class="ml-4 text-sm underline bg-transparent" aria-label="Switch Language">
                {{ app()->getLocale() == 'ar' ? 'English' : 'عربي' }}
            </button>
            <form id="langForm" method="POST" action="{{ route('lang.switch') }}" class="hidden">
                @csrf
                <input type="hidden" name="lang" value="{{ app()->getLocale() == 'ar' ? 'en' : 'ar' }}" />
                @if (Route::has('password.request'))
      <div class="mt-2 text-end">
        <a href="{{ route('password.request') }}" class="small text-primary fw-semibold">{{ __('Forgot your password?') }}</a>
      </div>
    @endif
</form>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-blue-900 text-white text-center py-4 mt-12">
        <p>© 2025 Sawaed Emirates Volunteer Society. All rights reserved.</p>
    </footer>
    @stack('scripts')
</body>
</html>
