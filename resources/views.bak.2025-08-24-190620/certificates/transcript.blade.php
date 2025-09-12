<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body{ font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2{ margin-bottom:6px; }
    table{ width:100%; border-collapse:collapse; }
    th,td{ border:1px solid #ccc; padding:6px; }
    th{ background:#f6f6f6; }
    .muted{ color:#666; }
  </style>
</head>
<body>
  <h2>{{ $site }} — {{ __('Volunteer Transcript') }}</h2>
  <p class="muted">{{ $user->name }} ({{ $user->volunteer_code }}) — {{ $user->email }}</p>
  <table>
    <thead><tr><th>#</th><th>{{ __('Opportunity ID') }}</th><th>{{ __('Minutes') }}</th><th>{{ __('Hours') }}</th><th>{{ __('Source') }}</th><th>{{ __('Locked') }}</th><th>{{ __('Updated') }}</th></tr></thead>
    <tbody>
      @php $i=1; @endphp
      @foreach($rows as $r)
        <tr>
          <td>{{ $i++ }}</td>
          <td>{{ $r->oid ?? $r->opportunity_id }}</td>
          <td>{{ $r->minutes }}</td>
          <td>{{ number_format($r->minutes/60,2) }}</td>
          <td>{{ $r->source }}</td>
          <td>{{ $r->locked ? 'Yes':'No' }}</td>
          <td>{{ $r->updated_at }}</td>
        </tr>
      @endforeach
      <tr>
        <td colspan="3"><strong>{{ __('Total Hours') }}</strong></td>
        <td colspan="4"><strong>{{ number_format($total,2) }}</strong></td>
      </tr>
    </tbody>
  </table>
</body>
</html>
