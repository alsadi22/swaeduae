<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Completion Report — {{ $opp->title }}</title>
<style>
 body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;background:#faf7ef;margin:0}
 .wrap{max-width:860px;margin:3rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.06)}
 h1{margin:0 0 1rem} .ok{background:#f1f8ff;color:#084298;padding:.6rem .8rem;border-radius:10px;margin:.5rem 0}
 .err{background:#fff2f0;color:#b02121;padding:.6rem .8rem;border-radius:10px;margin:.5rem 0}
 label{display:block;margin:.6rem 0 .2rem} input[type=file]{padding:.5rem;border:1px solid #ddd;border-radius:10px}
 .btn{display:inline-block;margin-top:1rem;padding:.6rem .9rem;border-radius:10px;background:#1e66f5;color:#fff;border:0}
 .muted{color:#666}
</style></head><body>
<div class="wrap">
  <h1>Completion Report — {{ $opp->title }}</h1>
  @if (session('status')) <div class="ok">{{ session('status') }}</div> @endif
  @if ($errors->any()) <div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div> @endif

  <p class="muted">Upload CSV with columns: <code>email,event_id,check_in_at,check_out_at,minutes</code> (minutes optional).</p>

  <form method="POST" action="{{ route('org.complete.store',$opp->id) }}" enctype="multipart/form-data">
    @csrf
    <label>CSV file</label>
    <input type="file" name="csv" accept=".csv,text/csv" required>
    <button class="btn" type="submit">Process & Issue Certificates</button>
  </form>
</div>
</body></html>
