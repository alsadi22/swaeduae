@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Upload KYC Document</h2>
    <form action="{{ route('kyc.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="kyc_file" required>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>
@endsection
