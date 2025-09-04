@extends("layouts.admin-argon")
<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Pending Organizations</title>
<meta name="viewport" content="width=device-width, initial-scale=1"></head><body style="font:16px system-ui;padding:20px;max-width:1100px;margin:0 auto">
<h1>Pending Organizations</h1>
<table border="1" cellpadding="6" cellspacing="0" width="100%">
  <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Logo</th><th>License</th><th>Actions</th></tr></thead>
  <tbody>
  @foreach($rows as $o)
    <tr>
      <td>{{ $o->id }}</td>
      <td>{{ $o->name }}</td>
      <td>{{ $o->email }}</td>
      <td>@if($o->logo_path)<a href="{{ Storage::url($o->logo_path) }}" target="_blank">Logo</a>@endif</td>
      <td>@if($o->license_path)<a href="{{ Storage::url($o->license_path) }}" target="_blank">License</a>@endif</td>
      <td>
        <form method="post" action="{{ route('admin.orgs.approve',$o) }}" style="display:inline">@csrf<button>Approve</button></form>
        <form method="post" action="{{ route('admin.orgs.reject',$o) }}" style="display:inline">@csrf<input name="notes" placeholder="Notes"><button>Reject</button></form>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $rows->links() }}
</body></html>
