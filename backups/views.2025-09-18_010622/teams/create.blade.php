@extends('layouts.app')

@section('content')
<h2>Create Team</h2>

@if ($errors->any())
    <div class="bg-red-200 p-3 rounded mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li class="text-red-700">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('teams.store') }}" method="POST">
    @csrf
    <label>Name:</label>
    <input type="text" name="name" value="{{ old('name') }}" class="border p-2 w-full mb-3" required />
    <label>Description:</label>
    <textarea name="description" class="border p-2 w-full mb-3">{{ old('description') }}</textarea>
    <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Create Team</button>
</form>
@endsection
