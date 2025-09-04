@extends('org.layout')
@section('content')
<div class="container py-4">
  <h1 class="h5 mb-3">{{ __('Team') }}</h1>
  @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif

  <form method="POST" action="{{ route('org.team.invite') }}" class="card shadow-sm mb-3">
    @csrf
    <div class="card-body row g-2 align-items-end">
      <div class="col-12 col-md-5">
        <label class="form-label">{{ __('Email') }}</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">{{ __('Role') }}</label>
        <select class="form-select" name="role"><option value="org_manager">org_manager</option></select>
      </div>
      <div class="col-12 col-md-2">
        <button class="btn btn-primary w-100">{{ __('Invite/Add') }}</button>
      </div>
    </div>
  </form>

  <div class="card shadow-sm">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>{{ __('Name') }}</th><th>Email</th><th>Role</th><th></th></tr></thead>
        <tbody>
          @forelse($members as $m)
            <tr>
              <td>{{ $m->name }}</td><td>{{ $m->email }}</td><td>{{ $m->role }}</td>
              <td class="text-end">
                <form method="POST" action="{{ route('org.team.remove',$m->user_id) }}" onsubmit="return confirm('{{ __('Remove?') }}')">@csrf
                  <button class="btn btn-sm btn-outline-danger">{{ __('Remove') }}</button>
                </form>
              </td>
            </tr>
          @empty <tr><td colspan="4" class="text-center text-muted p-3">{{ __('No team members yet') }}</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
