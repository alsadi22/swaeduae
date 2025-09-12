@extends('public.layout-travelpro')
@section('content')
<section class="container py-5">
  <h1>Verify Certificate</h1>
  <form action="{{ route('qr.verify',['code'=>'']) }}" method="GET" class="d-flex gap-2">
    <input name="code" class="form-control" placeholder="Enter verification code" required>
    <button class="btn btn-primary">Verify</button>
  </form>
</section>
@endsection
