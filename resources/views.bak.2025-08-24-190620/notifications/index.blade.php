@extends('layouts.app')

@section('title', __('Notifications'))

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ __('Notifications') }}
    @if(isset($unreadCount) && $unreadCount>0)
      <span class="badge bg-danger">{{ $unreadCount }}</span>
    @endif
  </h1>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="mb-3 d-flex gap-2">
    <form method="POST" action="{{ route('notifications.readAll') }}">
      @csrf
      <button class="btn btn-sm btn-outline-primary">{{ __('Mark all as read') }}</button>
    </form>
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('notifications.test') }}">{{ __('Send test') }}</a>
  </div>

  @forelse ($notifications as $n)
    <div class="card mb-2 {{ is_null($n->read_at) ? 'border-primary' : '' }}">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold">{{ data_get($n->data,'title', __('Notification')) }}</div>
            <div class="text-muted small">{{ data_get($n->data,'body') }}</div>
            <div class="text-secondary small">{{ $n->created_at->diffForHumans() }}</div>
          </div>
          @if(is_null($n->read_at))
            <form method="POST" action="{{ route('notifications.read', $n->id) }}">
              @csrf
              <button class="btn btn-sm btn-outline-success">{{ __('Mark read') }}</button>
            </form>
          @else
            <span class="badge bg-secondary">{{ __('Read') }}</span>
          @endif
        </div>
      </div>
    </div>
  @empty
    <p class="text-muted">{{ __('No notifications yet.') }}</p>
  @endforelse

  <div class="mt-3">
    {{ $notifications->links() }}
  </div>
</div>
@endsection
