@extends('admin.layout')
@section('title','Roles & Permissions')
@section('page_title','Roles & Permissions')

@section('content')
@if(session('ok')) <div class="alert alert-success">{{ session('ok') }}</div> @endif
@if ($errors->any())
  <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header fw-semibold">Roles</div>
      <div class="card-body">
        <form class="row g-2 mb-3" method="POST" action="{{ route('admin.roles.store') }}">@csrf
          <div class="col-8"><input class="form-control" name="name" placeholder="New role (e.g. org_manager)" required></div>
          <div class="col-4 d-grid"><button class="btn btn-teal">Add Role</button></div>
        </form>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Role</th><th>Users</th><th>Permissions</th></tr></thead>
            <tbody>
              @foreach($roles as $r)
                <tr>
                  <td class="fw-semibold">{{ $r->name }}</td>
                  <td>{{ $r->users_count }}</td>
                  <td>
                    <form method="POST" action="{{ route('admin.roles.perms',$r->id) }}">@csrf
                      <div class="d-flex flex-wrap gap-3">
                        @foreach($perms as $p)
                          <label class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $p->name }}" @checked($r->hasPermissionTo($p->name))>
                            <span class="form-check-label">{{ $p->name }}</span>
                          </label>
                        @endforeach
                      </div>
                      <div class="mt-2"><button class="btn btn-sm btn-outline-secondary">Save</button></div>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-header fw-semibold">Create Permission</div>
      <div class="card-body">
        <form class="row g-2" method="POST" action="{{ route('admin.perms.store') }}">@csrf
          <div class="col-8"><input class="form-control" name="name" placeholder="e.g. kyc.review" required></div>
          <div class="col-4 d-grid"><button class="btn btn-teal">Add Permission</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
