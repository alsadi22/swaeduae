@extends('public.layout')
@section('title','Volunteer Sign In')
@section('content')
<section class="min-h-screen flex items-center justify-center py-20 px-4">
  <div class="max-w-md w-full space-y-8" data-aos="fade-up">
    <div class="text-center">
      <div class="mx-auto w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
        <i data-feather="users" class="text-indigo-600 w-10 h-10"></i>
      </div>
      <h2 class="text-3xl font-bold text-gray-900">Volunteer Sign In</h2>
      <p class="mt-2 text-gray-600">Access your volunteer portal</p>
    </div>

    @if (session('status'))
      <div class="text-green-600 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="text-red-600 text-sm mb-2">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6 bg-white p-8 rounded-xl shadow-md">
      @csrf
      <div class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" required
                 class="form-input mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                 placeholder="Enter your email">
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
          <input id="password" name="password" type="password" required
                 class="form-input mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                 placeholder="Enter your password">
        </div>
      </div>

      <div class="flex items-center justify-between">
        <label class="flex items-center">
          <input id="remember" name="remember" type="checkbox"
                 class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
          <span class="ml-2 text-sm text-gray-700">Remember me</span>
        </label>
        <div class="text-sm">
          <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot password?</a>
        </div>
      </div>

      <button type="submit"
              class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        Sign In
      </button>

      @if (config('services.google') && \Illuminate\Support\Facades\Route::has('socialite.redirect'))
        <a href="{{ route('socialite.redirect','google') }}"
           class="w-full mt-3 inline-flex items-center justify-center bg-white border border-gray-300 py-2 rounded-lg hover:bg-gray-50">
          <img src="https://developers.google.com/identity/images/g-logo.png" class="w-5 h-5 mr-2" alt="">
          Sign in with Google
        </a>
      @endif

      <div class="text-center">
        <p class="text-sm text-gray-600">
          Not a volunteer yet?
          <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Create an account</a>
        </p>
      </div>
    </form>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-8">
      <div class="flex">
        <div class="flex-shrink-0">
          <i data-feather="info" class="h-5 w-5 text-blue-400"></i>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-blue-800">Need help accessing your account?</h3>
          <div class="mt-2 text-sm text-blue-700">
            <p>volunteers@swaeduae.ae • +971 XX XXX XXXX</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
