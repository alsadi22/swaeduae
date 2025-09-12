@extends('layouts.app')
@section('title', 'Payment Successful')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow text-center">
    <h1 class="text-2xl font-bold mb-6">Thank you!</h1>
    <p>Your payment was successful.</p>
    <a href="{{ route('home') }}" class="text-blue-700 underline mt-4 inline-block">Return Home</a>
</div>
@endsection
