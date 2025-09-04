@extends('admin.layout')
@section('content')
<div class="max-w-2xl mx-auto mt-10 p-8 bg-white shadow rounded">
    <h2 class="text-2xl font-bold mb-6">Site Settings</h2>
    <form>
        <label class="block mb-2 font-semibold">Site Name:</label>
        <input type="text" name="app_name" value="{{ config('app.name') }}" class="mb-4" disabled>
        <label class="block mb-2 font-semibold">Contact Email:</label>
        <input type="email" name="contact_email" value="info@swaeduae.ae" class="mb-4" disabled>
        <label class="block mb-2 font-semibold">Default Language:</label>
        <input type="text" name="locale" value="{{ app()->getLocale() }}" class="mb-4" disabled>
        <p class="text-gray-400">Editing disabled until backend is connected.</p>
    </form>
</div>
@endsection
