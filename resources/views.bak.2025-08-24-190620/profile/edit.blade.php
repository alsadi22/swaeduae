<!doctype html>
<html>
<head><meta charset="utf-8"><title>My Profile</title>
<style>body{font-family:system-ui,Arial;margin:2rem} .card{max-width:760px;margin:auto;padding:1.25rem;border:1px solid #eee;border-radius:10px} ul{line-height:1.9}</style>
</head>
<body>
  <div class="card">
    <h2>My Profile</h2>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <hr>
    <ul>
      @if (\Illuminate\Support\Facades\Route::has('certificates.my'))
        <li><a href="{{ route('certificates.my') }}">My certificates</a></li>
      @endif
      @if (\Illuminate\Support\Facades\Route::has('my.hours'))
        <li><a href="{{ route('my.hours') }}">My volunteer hours</a></li>
      @endif
      @if (\Illuminate\Support\Facades\Route::has('opportunities.index'))
        <li><a href="{{ route('opportunities.index') }}">Opportunities</a></li>
      @else
        <li><a href="/opportunities">Opportunities</a></li>
      @endif
    </ul>
  </div>
</body>
</html>
