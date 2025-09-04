@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-12 p-6 bg-white rounded shadow">
    <h2 class="text-2xl mb-6 font-bold text-center">Forgot Your Password?</h2>

    @if (session('status'))
        <div class="bg-green-100 p-3 mb-4 rounded text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block mb-1 font-semibold">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border rounded px-3 py-2" />
            @error('email')<p class="text-red-600 mt-1 text-sm">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full bg-blue-700 text-white py-2 rounded hover:bg-blue-900">
            Send Password Reset Link
        </button>
        @if (Route::has('password.request'))
      <div class="mt-2 text-end">
        <a href="{{ route('password.request') }}" class="small text-primary fw-semibold">{{ __('Forgot your password?') }}</a>
      </div>
    @endif
</form>

    <p class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Back to Login</a>
    </p>
</div>
@endsection
