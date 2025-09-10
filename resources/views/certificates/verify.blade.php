<!doctype html>
<html lang="en"><head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Verify Certificate</title>
</head><body>
  <main style="padding:2rem;font-family:sans-serif;max-width:720px;margin:auto">
    <h1>Verify Certificate</h1>
    <form method="GET" action="/certificates/verify" style="margin:.75rem 0 1.25rem">
      <input name="code" placeholder="Code" value="{{ request(code) }}" style="padding:.5rem;width:70%;max-width:420px">
      <button type="submit" style="padding:.55rem 1rem">Verify</button>
    </form>
    @if (request(code))
      <p>Verification service is being finalized.</p>
    @endif
  </main>
</body></html>
