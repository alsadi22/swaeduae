@extends('layouts.app')
@section('title', 'Make a Payment')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-6">Make a Payment</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    <form action="{{ route('payments.processStripe') }}" method="POST" class="mb-4">
        @csrf
        <label class="block mb-2 font-bold">Amount (Stripe)</label>
        <input type="number" name="amount" step="0.01" required class="w-full border px-3 py-2 rounded mb-2" />
        <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded">Pay with Stripe</button>
    </form>
    <form action="{{ route('payments.processPayTabs') }}" method="POST">
        @csrf
        <label class="block mb-2 font-bold">Amount (PayTabs)</label>
        <input type="number" name="amount" step="0.01" required class="w-full border px-3 py-2 rounded mb-2" />
        <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded">Pay with PayTabs</button>
    </form>
</div>
@endsection
