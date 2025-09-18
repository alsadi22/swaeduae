<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Volunteer Sign In - SWA Education UAE') }}</title>
    <link rel="icon" type="image/x-icon" href="/static/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .form-input:focus {
            outline: none;
            ring: 2px;
            ring-color: #667eea;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-600">SWA Education</a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">{{ __('Home') }}</a>
                    <a href="{{ url('/about') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">{{ __('About') }}</a>
                    <a href="{{ url('/opportunities') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">{{ __('Opportunities') }}</a>
                    <a href="{{ url('/login') }}" class="text-indigo-600 font-medium">{{ __('Volunteer Sign In') }}</a>
                    <a href="{{ url('/contact') }}" class="text-gray-600 hover:text-indigo-600 transition-colors">{{ __('Contact') }}</a>
                </div>
                <div class="md:hidden">
                    <button class="mobile-menu-button">
                        <i data-feather="menu"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sign In Section -->
    <section class="min-h-screen flex items-center justify-center py-20 px-4">
        <div class="max-w-md w-full space-y-8" data-aos="fade-up">
            <div class="text-center">
                <div class="mx-auto w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                    <i data-feather="users" class="text-indigo-600 w-10 h-10"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">{{ __('Volunteer Sign In') }}</h2>
                <p class="mt-2 text-gray-600">{{ __('Access your volunteer portal') }}</p>
            </div>
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form class="mt-8 space-y-6 bg-white p-8 rounded-xl shadow-md" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email Address') }}</label>
                        <input id="email" name="email" type="email" required 
                               class="form-input mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password" required 
                               class="form-input mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="{{ __('Enter your password') }}">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">{{ __('Remember me') }}</label>
                    </div>

                    <div class="text-sm">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Forgot password?') }}</a>
                        @else
                            <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Forgot password?') }}</a>
                        @endif
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Sign In') }}
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        {{ __('Not a volunteer yet?') }} 
                        <a href="{{ url('/contact') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Join our team') }}</a>
                    </p>
                </div>
            </form>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-feather="info" class="h-5 w-5 text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">{{ __('Need help accessing your account?') }}</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>{{ __('Contact our volunteer coordinator at') }} volunteers@swaeduae.ae {{ __('or call') }} +971 X XXX XXXX</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">SWA Education</h3>
                    <p class="text-gray-400">{{ __('Empowering students through quality education in the UAE') }}</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">{{ __('Quick Links') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('Home') }}</a></li>
                        <li><a href="{{ url('/about') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('About') }}</a></li>
                        <li><a href="{{ url('/opportunities') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('Opportunities') }}</a></li>
                        <li><a href="{{ url('/login') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('Volunteer Sign In') }}</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-gray-400 hover:text-white transition-colors">{{ __('Contact') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">{{ __('Contact Info') }}</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: info@swaeduae.ae</li>
                        <li>Phone: +971 X XXX XXXX</li>
                        <li>Dubai, UAE</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">{{ __('Follow Us') }}</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i data-feather="facebook"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i data-feather="twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i data-feather="instagram"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i data-feather="linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} SWA Education. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>

    <script>
        AOS.init({
            duration: 800,
            once: true
        });
        feather.replace();
    </script>
</body>
</html>
