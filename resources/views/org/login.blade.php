<!doctype html>
<html lang="{{ str_replace("_","-",app()->getLocale()) }}">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ __("Organization Sign In - SwaedUAE") }}</title>
  <link rel="icon" href="/static/favicon.ico">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>.fi:focus{outline:none;box-shadow:0 0 0 2px rgba(99,102,241,.4)}</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
  @php
    $action = \Illuminate\Support\Facades\Route::has("org.login.perform") ? route("org.login.perform")
            : (\Illuminate\Support\Facades\Route::has("org.login") ? route("org.login") : url("/org/login"));
  @endphp
  <form method="POST" action="{{ $action }}" class="w-full max-w-md bg-white shadow rounded-2xl p-8 space-y-5">
    @csrf
    @if ($errors->any())
      <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded p-3">
        <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
      </div>
    @endif

    <h1 class="text-2xl font-bold text-center">{{ __("Organization Sign In") }}</h1>

    <label class="block text-sm">{{ __("Business Email") }}
      <input id="email" name="email" type="email" required autofocus
             class="fi mt-1 w-full border border-gray-300 rounded px-3 py-2"
             value="{{ old("email") }}">
    </label>

    <label class="block text-sm">{{ __("Password") }}
      <input id="password" name="password" type="password" required
             class="fi mt-1 w-full border border-gray-300 rounded px-3 py-2">
    </label>

    <label class="inline-flex items-center text-sm">
      <input type="checkbox" name="remember" class="mr-2">{{ __("Remember me") }}
    </label>

    <div class="flex items-center justify-between">
      @if (Route::has("password.request"))
        <a href="{{ route("password.request") }}" class="text-sm text-indigo-600 hover:underline">{{ __("Forgot?") }}</a>
      @endif
      <button type="submit" class="ml-auto px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __("Sign In") }}</button>
    </div>

    <p class="text-center text-sm text-gray-600">
      {{ __("No org account?") }}
      <a href="{{ url("/org/register") }}" class="text-indigo-600 hover:underline">{{ __("Submit for approval") }}</a>
    </p>
  </form>
</body>
</html>