@extends('layouts.app')

@section('title',$story->title)

@section('content')
<h1>{{ $story->title }}</h1>
{!! nl2br(e($story->body)) !!}
@endsection
