@extends('admin.layout')

@section('content')
<h1>Add News</h1>
<form method="POST" action="{{ route('admin.news.store') }}">
@csrf
<label>Title</label>
<input type="text" name="title" required />
<label>Content</label>
<textarea name="content" required></textarea>
<button type="submit">Create</button>
</form>
@endsection
