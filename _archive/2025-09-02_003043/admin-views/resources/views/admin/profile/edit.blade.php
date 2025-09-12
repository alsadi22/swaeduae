@extends('admin.layout') {{-- or your admin master layout --}}
@section('title','My Profile')

@section('content')
<div class="card">
    <div class="card-header"><strong>My Profile</strong></div>
    <div class="card-body">
        @if(session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">New Password <small class="text-muted">(optional)</small></label>
                    <input name="password" type="password" class="form-control" autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <input name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Profile Photo</label>
                    <input name="profile_photo" type="file" class="form-control">
                    @if($user->profile_photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$user->profile_photo) }}" alt="Avatar" style="height:64px;border-radius:8px">
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
