#!/bin/bash
# SwaedUAE Laravel Updates Script
# Applies all recent fixes (no Vite, Blade component slot fix, navigation fix, Bootstrap CDN)

echo "Updating layouts/app.blade.php..."
cat <<'EOF' > /home3/vminingc/swaeduae.ae/laravel-app/resources/views/layouts/app.blade.php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SwaedUAE') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Your own CSS in public/css/app.css -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Optional: Your own JS in public/js/app.js -->
    <script src="{{ asset('js/app.js') }}"></script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>
</html>
EOF

echo "Updating layouts/navigation.blade.php..."
cat <<'EOF' > /home3/vminingc/swaeduae.ae/laravel-app/resources/views/layouts/navigation.blade.php
<!-- Settings Dropdown -->
<div class="hidden sm:flex sm:items-center sm:ms-6">
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md 
text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                @if(Auth::check())
                    <div>{{ Auth::user()->name }}</div>
                @else
                    <div>Guest</div>
                @endif
                <div class="ms-1">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 
01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>
        <!-- Your dropdown content here -->
    </x-dropdown>
</div>
EOF

echo "Updating components/dropdown.blade.php..."
cat <<'EOF' > /home3/vminingc/swaeduae.ae/laravel-app/resources/views/components/dropdown.blade.php
<div x-data="{ open: false }" class="relative">
    <!-- Trigger slot -->
    <div @click="open = ! open">
        {{ $trigger ?? '' }}
    </div>
    <!-- Dropdown content -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $width ?? '' }} rounded-md shadow-lg {{ $alignmentClasses ?? '' }}"
        style="display: none;"
        @click.away="open = false"
    >
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses ?? '' }}">
            {{ $slot }}
        </div>
    </div>
</div>
EOF

echo "All updates applied! Please refresh your website to see the changes."

