<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Submitted — SwaedUAE</title>
<style>
  body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial,sans-serif;background:#faf7ef;margin:0}
  .wrap{max-width:640px;margin:5rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.06)}
  h1{margin:0 0 1rem}
  a.btn{display:inline-block;margin-top:1rem;padding:.6rem .9rem;border-radius:10px;background:#1e66f5;color:#fff;text-decoration:none}
</style></head><body>
  <div class="wrap">
    <h1>Thanks!</h1>
    <p>Your organization <strong>{{ $org }}</strong> has been submitted for review.</p>
    <p>We’ll email <strong>{{ $email }}</strong> as soon as it’s approved. You can sign in anytime to check.</p>
    <a class="btn" href="{{ url('/') }}">Back to Home</a>
  </div>
</body></html>
