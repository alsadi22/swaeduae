{{-- resources/views/partials/account_menu.blade.php --}}
@php
    use Illuminate\Support\Str;
    $user = auth()->user();
    $logoutUrl = \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout');
@endphp

@auth
<div class="relative group">
    <button class="flex items-center space-x-3 bg-gray-100 rounded-xl px-4 py-2 hover:bg-gray-200 transition-colors">
        <img src="https://i.pravatar.cc/40?u={{ $user->id ?? 0 }}" alt="Profile" class="w-8 h-8 rounded-full">
        <span class="text-gray-800 font-medium">
            {{ Str::of($user->name ?? 'User')->limit(10) }}
        </span>
        <i data-feather="chevron-down" class="w-4 h-4"></i>
    </button>
    <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-gray-100">
        <a href="{{ route('volunteer.profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">My Profile</a>
        <a href="{{ route('volunteer.settings') }}" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Settings</a>
        <form method="POST" action="{{ $logoutUrl }}" class="block">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Sign Out</button>
        </form>
    </div>
</div>
@else
<div class="flex items-center space-x-3">
    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-600 hover:text-indigo-600">Sign In</a>
    <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Register</a>
</div>
@endauth

<script>document.addEventListener('DOMContentLoaded',()=>window.feather&&feather.replace());</script>
