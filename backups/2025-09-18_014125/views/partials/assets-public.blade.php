{{-- Public CSS/Meta for SwaedUAE (safe restore) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

{{-- Load your site stylesheet(s) if present --}}
@if (file_exists(public_path('assets/css/app.css')))
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ @filemtime(public_path('assets/css/app.css')) }}">
@endif
@if (file_exists(public_path('css/app.css')))
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ @filemtime(public_path('css/app.css')) }}">
@endif
