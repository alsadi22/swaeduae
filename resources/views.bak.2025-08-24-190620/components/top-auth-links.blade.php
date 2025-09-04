@if (Route::has('login'))
  <a href="{{ route('login') }}" class="text-blue-700 font-bold mr-4">Login</a>
@elseif (Route::has('login.volunteer'))
  <a href="{{ route('login.volunteer') }}" class="text-blue-700 font-bold mr-4">Login</a>
@elseif (Route::has('org.login'))
  <a href="{{ route('org.login') }}" class="text-blue-700 font-bold mr-4">Login</a>
@else
  <a href="{{ url('/signin') }}" class="text-blue-700 font-bold mr-4">Login</a>
@endif

@if (Route::has('register'))
  <a href="{{ route('register') }}" class="bg-blue-700 text-white px-4 py-2 rounded-lg font-bold">Sign Up</a>
@elseif (Route::has('org.register'))
  <a href="{{ route('org.register') }}" class="bg-blue-700 text-white px-4 py-2 rounded-lg font-bold">Sign Up</a>
@else
  <a href="{{ url('/signup') }}" class="bg-blue-700 text-white px-4 py-2 rounded-lg font-bold">Sign Up</a>
@endif
