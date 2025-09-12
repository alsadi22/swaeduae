<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Applications — SwaedUAE</title>
<style>
 body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;background:#faf7ef;margin:0}
 .wrap{max-width:960px;margin:3rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 10px 24px rgba(0,0,0,.06)}
 h1{margin:0 0 1rem}
 table{width:100%;border-collapse:collapse}
 th,td{padding:.6rem;border-bottom:1px solid #eee;text-align:left;font-size:.95rem}
 .pill{display:inline-block;border-radius:999px;padding:.2rem .6rem;background:#eef2ff;color:#1e40af}
 .muted{color:#666}
</style></head><body>
<div class="wrap">
  <h1>My Applications</h1>
  @if ($rows->count()===0)
    <p class="muted">You have no applications yet.</p>
  @else
    <table>
      <thead><tr><th>Opportunity</th><th>Location</th><th>Dates</th><th>Status</th><th>Applied</th></tr></thead>
      <tbody>
        @foreach ($rows as $r)
          <tr>
            <td>{{ $r->title }}</td>
            <td>{{ $r->location }}</td>
            <td>{{ optional($r->starts_at)->format('Y-m-d') }} – {{ optional($r->ends_at)->format('Y-m-d') }}</td>
            <td><span class="pill">{{ $r->status }}</span></td>
            <td class="muted">{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    {{ $rows->links() }}
  @endif
</div>
</body></html>
