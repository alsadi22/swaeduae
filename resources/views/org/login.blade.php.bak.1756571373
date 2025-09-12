<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Organization Login</title>
<link rel="stylesheet" href="/vendor/argon/assets/css/argon-dashboard.min.css">
<style>body{background:#f8fafc}.card{max-width:480px;margin:6rem auto}</style>
</head><body>
<form method="POST" action="{{ route('org.login.perform') }}" class="card p-4 shadow">
  @csrf
  <h1 class="h4 mb-3 text-center">Organization Login</h1>
  <div class="mb-3"><label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="mb-3"><label class="form-label">Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button class="btn btn-primary w-100">Sign in</button>
</form>
</body></html>
