@extends('public.layout')
@section('title','{{ __('Contact') }}')
@section('content')
<section class="py-16"><div class="wrap">
@extends('public.layout-travelpro')
@section('title', __('Contact'))
@section('meta_description','Get in touch with the SwaedUAE team.')
@section('content')
<div class="container py-5">
  <h1 class="h3 mb-3">{{ __('Contact') }}</h1>
  @if (session('status'))
    <div id="thanks" class="alert alert-success">{{ session('status') }}</div>
    <script>
      document.addEventListener('DOMContentLoaded',function(){
        const el=document.getElementById('thanks');
        if(el) el.scrollIntoView({behavior:'smooth'});
      });
    </script>
  @endif
  @if ($errors->any()) <div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div> @endif

  <form method="POST" action="{{ route('contact.submit') }}" class="mt-3">
    @csrf
    <input type="text" name="__website" style="display:none" autocomplete="off">
    <div class="mb-3">
      <label class="form-label">Your Name</label>
      <input class="form-control" name="name" value="{{ session('clear') ? '' : old('name') }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" value="{{ session('clear') ? '' : old('email') }}" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea class="form-control" name="message" rows="6" required>{{ session('clear') ? '' : old('message') }}</textarea>
    </div>
    <button class="btn btn-primary" type="submit">Send</button>
  </form>
</div>
@endsection

</div></section>
@endsection
