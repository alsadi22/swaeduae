@extends('admin.layout')
@section('title', __('Users'))
@section('page_title', __('Users'))
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow border-0">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ __('Users') }}</h6>
        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Refresh') }}</a>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Name') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Email') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Joined') }}</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">{{ __('Actions') }}</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users ?? [] as $u)
                <tr>
                  <td class="text-sm">{{ $u->id }}</td>
                  <td class="text-sm">{{ $u->name }}</td>
                  <td class="text-sm">{{ $u->email }}</td>
                  <td class="text-sm">{{ optional($u->created_at)->format('Y-m-d') }}</td>
                  <td class="text-end">
                    <a href="{{ route('admin.users.edit',$u->id) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-secondary p-4">— {{ __('No users yet') }} —</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if(method_exists(($users ?? null), 'links'))
        <div class="card-footer d-flex justify-content-end">
          {{ $users->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
