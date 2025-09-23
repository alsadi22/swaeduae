<!doctype html><meta charset="utf-8"><title>Register</title>
<style>body{font-family:system-ui;margin:3rem}form{max-width:520px}label{display:block;margin:.6rem 0}</style>
<h2>Create account</h2>

<form method="POST" action="{{ url('/register') }}">
  @csrf
  <label>Name <input type="text" name="name" required></label>
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <label>Confirm <input type="password" name="password_confirmation" required></label>
  <button type="submit">Register</button>
</form>
