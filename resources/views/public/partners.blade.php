<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Our Partners — SwaedUAE</title>
<style>
 body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;background:#faf7ef;margin:0}
 .wrap{max-width:1100px;margin:3rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.06)}
 h1{margin:0 0 1rem} p.muted{color:#666;margin:.5rem 0 1.5rem}
 .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:18px}
 .card{display:flex;align-items:center;justify-content:center;background:#f8fafc;border:1px solid #e5e7eb;border-radius:14px;padding:18px;height:120px}
 .card img{max-width:100%;max-height:80px;object-fit:contain}
</style></head><body>
<div class="wrap">
  <h1>Our Partners</h1>
  <p class="muted">Organizations that support SwaedUAE’s volunteer community.</p>
  @php
    use Illuminate\Support\Facades\Storage;
    $files = collect(Storage::files('public/partners'))
      ->filter(fn($p) => preg_match('/\.(png|jpe?g|svg)$/i',$p))
      ->map(fn($p) => Storage::url($p));
  @endphp
  @if($files->isEmpty())
    <p class="muted">No partner logos uploaded yet. Add images to <code>storage/app/public/partners</code>.</p>
  @else
    <div class="grid">
      @foreach($files as $src)
        <div class="card"><img src="{{ $src }}" alt="Partner logo"></div>
      @endforeach
    </div>
  @endif
</div>
    <script src="/assets/nav-dropdown-fix.js"></script>
</body></html>
    <script src="/assets/feather.min.js"></script>
    <script>document.addEventListener("DOMContentLoaded",function(){ if(window.feather&&feather.replace) feather.replace();});</script>
