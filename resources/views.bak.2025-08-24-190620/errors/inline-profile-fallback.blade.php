<!doctype html>
<html><head><meta charset="utf-8"><title>Profile</title></head>
<body>
  <h1>{{ __('My Profile') }}</h1>
  <p><strong>{{ __('Name') }}:</strong> {{ $user->name ?? '—' }}</p>
  <p><strong>{{ __('Email') }}:</strong> {{ $user->email ?? '—' }}</p>
</body>
</html>
