<!doctype html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title','SwaEduAE Volunteering Society') | UAE Volunteer Platform</title>

  <!-- Tailwind + AOS + Feather -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>

  <style>
    :root{
      --primary:#4f46e5;--primary-dark:#4338ca;--primary-light:#6366f1;
      --secondary:#10b981;--secondary-dark:#059669;--accent:#f59e0b;
      --dark:#1f2937;--light:#f9fafb;--gray:#6b7280;
      --success:#10b981;--warning:#f59e0b;--error:#ef4444;--info:#3b82f6;
    }
    .hero-gradient{background:linear-gradient(135deg,var(--primary) 0%,var(--secondary) 100%)}
    .volunteer-card{transition:all .3s cubic-bezier(.4,0,.2,1);border:1px solid #e5e7eb}
    .volunteer-card:hover{transform:translateY(-8px) scale(1.02);box-shadow:0 25px 50px -12px rgba(0,0,0,.25);border-color:var(--primary-light)}
    .testimonial-card{background:rgba(255,255,255,.1);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,.2);transition:all .3s}
    .testimonial-card:hover{transform:translateY(-4px);background:rgba(255,255,255,.15)}
    .btn-primary{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);transition:all .3s}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 10px 25px -5px rgba(79,70,229,.4)}
    .btn-secondary{background:linear-gradient(135deg,var(--secondary) 0%,var(--secondary-dark) 100%);transition:all .3s}
    .btn-secondary:hover{transform:translateY(-2px);box-shadow:0 10px 25px -5px rgba(16,185,129,.4)}
    .nav-link{position:relative;transition:color .3s}
    .nav-link::after{content:'';position:absolute;bottom:-2px;left:0;width:0;height:2px;background:var(--primary);transition:width .3s}
    .nav-link:hover::after{width:100%}
    .stats-card{transition:transform .3s,box-shadow .3s}
    .stats-card:hover{transform:translateY(-4px);box-shadow:0 20px 25px -5px rgba(0,0,0,.1)}
    .gradient-text{background:linear-gradient(135deg,var(--primary) 0%,var(--secondary) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
    .floating{animation:float 6s ease-in-out infinite}
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}
    .pulse{animation:pulse 2s cubic-bezier(.4,0,.6,1) infinite}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.7}}
    .fade-in{animation:fadeIn 1s ease-in}
    @keyframes fadeIn{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
    .glass-effect{background:rgba(255,255,255,.1);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.2)}
    .hover-lift{transition:transform .3s}
    .hover-lift:hover{transform:translateY(-4px)}
    .text-shadow{text-shadow:0 2px 4px rgba(0,0,0,.1)}
    .shadow-glow{box-shadow:0 0 20px rgba(79,70,229,.2)}
    .border-gradient{border:2px solid transparent;background:linear-gradient(white,white) padding-box,linear-gradient(135deg,var(--primary),var(--secondary)) border-box}
  </style>
</head>
<body class="bg-gray-50 scroll-smooth">

  <!-- Navigation -->
  <nav class="bg-white shadow-lg sticky top-0 z-50 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-20">
        <div class="flex items-center">
          <div class="flex-shrink-0 flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
              <i data-feather="heart" class="text-white h-6 w-6"></i>
            </div>
            <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-emerald-500 bg-clip-text text-transparent">SwaEduAE</span>
          </div>
        </div>
        <div class="hidden md:ml-6 md:flex md:items-center md:space-x-10">
          <a href="{{ url('/') }}"               class="nav-link text-gray-800 font-medium hover:text-indigo-600">Home</a>
          <a href="{{ url('/opportunities') }}"  class="nav-link text-gray-600 hover:text-indigo-600">Opportunities</a>
          <a href="{{ url('/about') }}"          class="nav-link text-gray-600 hover:text-indigo-600">About</a>
          <a href="#"                            class="nav-link text-gray-600 hover:text-indigo-600">Stories</a>
          <a href="{{ url('/organizations') }}"  class="nav-link text-gray-600 hover:text-indigo-600">Organizations</a>
          <a href="{{ url('/contact') }}"        class="nav-link text-gray-600 hover:text-indigo-600">Contact</a>
          <div class="flex items-center space-x-4">
            <div class="relative group">
              <button class="btn-primary px-6 py-3 rounded-xl text-white font-medium flex items-center space-x-2 shadow-lg hover:shadow-xl">
                <i data-feather="log-in" class="w-4 h-4"></i><span>Sign In</span>
                <i data-feather="chevron-down" class="w-4 h-4 transition-transform group-hover:rotate-180"></i>
              </button>
              <div class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 border border-gray-100">
                <a href="{{ url('/login') }}"     class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 flex items-center space-x-2"><i data-feather="user" class="w-4 h-4"></i><span>Volunteer Sign In</span></a>
                <a href="{{ url('/org/login') }}" class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 flex items-center space-x-2"><i data-feather="briefcase" class="w-4 h-4"></i><span>Organization Sign In</span></a>
              </div>
            </div>
            <div class="relative group">
              <button class="border-gradient px-6 py-3 rounded-xl text-indigo-600 font-medium flex items-center space-x-2 bg-white hover:bg-gray-50">
                <i data-feather="user-plus" class="w-4 h-4"></i><span>Sign Up</span>
                <i data-feather="chevron-down" class="w-4 h-4 transition-transform group-hover:rotate-180"></i>
              </button>
              <div class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 border border-gray-100">
                <a href="{{ url('/register') }}"     class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 flex items-center space-x-2"><i data-feather="user" class="w-4 h-4"></i><span>Volunteer Sign Up</span></a>
                <a href="{{ url('/org/register') }}" class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 flex items-center space-x-2"><i data-feather="briefcase" class="w-4 h-4"></i><span>Organization Sign Up</span></a>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center md:hidden">
          <button type="button" class="inline-flex items-center justify-center p-3 rounded-xl text-gray-600 hover:text-indigo-600 hover:bg-gray-100">
            <i data-feather="menu" class="h-6 w-6"></i>
          </button>
        </div>
      </div>
    </div>
  </nav>

  @yield('content')

  <!-- Footer -->
  <footer class="bg-gray-800">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
        <div>
          <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">About</h3>
          <ul class="mt-4 space-y-4">
            <li><a href="#" class="text-base text-gray-300 hover:text-white">Our Mission</a></li>
            <li><a href="#" class="text-base text-gray-300 hover:text-white">Partners</a></li>
            <li><a href="{{ url('/privacy') }}" class="text-base text-gray-300 hover:text-white">Privacy</a></li>
            <li><a href="{{ url('/terms') }}" class="text-base text-gray-300 hover:text-white">Terms</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Volunteer</h3>
          <ul class="mt-4 space-y-4">
            <li><a href="{{ url('/opportunities') }}" class="text-base text-gray-300 hover:text-white">Opportunities</a></li>
            <li><a href="{{ url('/qr/verify') }}" class="text-base text-gray-300 hover:text-white">QR Verify</a></li>
            <li><a href="{{ url('/certificates/verify') }}" class="text-base text-gray-300 hover:text-white">Certificate Verify</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Resources</h3>
          <ul class="mt-4 space-y-4">
            <li><a href="#" class="text-base text-gray-300 hover:text-white">Stories</a></li>
            <li><a href="{{ url('/contact') }}" class="text-base text-gray-300 hover:text-white">Contact</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Connect</h3>
          <ul class="mt-4 space-y-4">
            <li class="flex space-x-6 mt-2">
              <a href="#" class="text-gray-400 hover:text-white"><i data-feather="instagram"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i data-feather="twitter"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i data-feather="linkedin"></i></a>
            </li>
          </ul>
        </div>
      </div>
      <div class="mt-12 border-t border-gray-700 pt-8">
        <p class="text-base text-gray-400 text-center">
          &copy; {{ date('Y') }} SwaEduAE Volunteering Society. All rights reserved.
        </p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded',function(){
      try { AOS.init({ duration:800, easing:'ease-in-out', once:true }); } catch(e){}
      try { feather.replace(); } catch(e){}
    });
  </script>
  @yield('scripts')
</body>
</html>
