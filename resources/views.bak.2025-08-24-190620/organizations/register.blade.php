@extends('layouts.app')
@section('title','Register Organization')

@section('content')
<h1>Register Your Organization</h1>
<p>Submit your organization details. An admin will review and approve your account before you can post opportunities.</p>

<form class="card" method="POST" enctype="multipart/form-data" action="{{ route('organizations.register.store') }}">
    @csrf
    <label>Organization Name</label>
    <input name="name" value="{{ old('name') }}" required>

    <label>Email</label>
    <input name="email" type="email" value="{{ old('email') }}" required>

    <label>Phone</label>
    <input name="phone" value="{{ old('phone') }}">

    <label>Website</label>
    <input name="website" type="url" value="{{ old('website') }}">

    <label>Address</label>
    <input name="address" value="{{ old('address') }}">

    <label>City</label>
    <input name="city" value="{{ old('city') }}">

    <label>Emirate</label>
    <input name="emirate" value="{{ old('emirate') }}">

    <label>Logo</label>
    <input name="logo" type="file" accept="image/*">

    <label>Description</label>
    <textarea name="description" rows="5">{{ old('description') }}</textarea>

    <div style="margin-top:12px">
        <button class="btn" type="submit">Submit Application</button>
    </div>
</form>
@endsection
