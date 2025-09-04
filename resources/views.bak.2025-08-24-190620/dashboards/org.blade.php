@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Organization Dashboard</h1>
    <p>Welcome, {{ Auth::user()->name }}! Manage your events, volunteers, and reports here.</p>
</div>
@endsection
