<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin • Approvals</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
 body{font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;background:#f8fafc;color:#0f172a}
 header{background:#0f172a;color:#fff;padding:16px 20px}
 main{max-width:1000px;margin:24px auto;padding:0 16px}
 .card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px}
 table{width:100%;border-collapse:collapse}
 th,td{padding:10px;border-bottom:1px solid #e2e8f0;text-align:left}
 th{font-weight:600;background:#f1f5f9}
 .row-actions form{display:inline}
 .btn{padding:8px 12px;border-radius:8px;border:0;cursor:pointer}
 .approve{background:#16a34a;color:#fff} .reject{background:#dc2626;color:#fff}
 .badge{display:inline-block;padding:2px 8px;border-radius:9999px;background:#e2e8f0}
 .muted{color:#64748b;font-size:14px}
</style>
</head><body>
<header><strong>Admin • Organization Approvals</strong></header>
<main>
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
      <h2 style="margin:6px 0">Pending Organizations</h2>
      <span class="badge">{{ isset($pending)? count($pending):0 }} pending</span>
    </div>
    @if (session('status'))
      <p class="muted">Action: {{ session('status') }}</p>
    @endif
    @if(empty($pending) || count($pending)===0)
      <p class="muted">No pending organization profiles.</p>
    @else
    <div style="overflow:auto">
    <table>
      <thead><tr>
        <th>ID</th><th>Org Name</th><th>User Email</th><th>Status</th><th>Actions</th>
      </tr></thead>
      <tbody>
        @foreach ($pending as $p)
        <tr>
          <td>{{ $p->id }}</td>
          <td>{{ $p->org_name ?? '-' }}</td>
          <td>{{ $p->user_email ?? '-' }}</td>
          <td>{{ $p->status }}</td>
          <td class="row-actions">
            <form method="POST" action="{{ route('admin.approvals.orgs.approve',$p->id) }}" style="margin-right:6px;display:inline">
              @csrf <button class="btn approve" type="submit">Approve</button>
            </form>
            <form method="POST" action="{{ route('admin.approvals.orgs.reject',$p->id) }}" style="display:inline">
              @csrf <button class="btn reject" type="submit">Reject</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    @endif
  </div>
</main>
</body></html>
