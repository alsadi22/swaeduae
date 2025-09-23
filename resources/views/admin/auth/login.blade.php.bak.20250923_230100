@include('auth._social')

@if ($errors->any()) <div>{{ implode(', ', $errors->all()) }}</div> @endif
<form method="POST" action="{{ route('admin.login.perform') }}">@csrf
  <input name="email" type="email" required placeholder="Email" value="{{ old('email') }}"><br>
  <input name="password" type="password" required placeholder="Password"><br>
  <label><input type="checkbox" name="remember"> Remember me</label><br>
  <button type="submit">Sign in</button>
</form>
</body></html>
