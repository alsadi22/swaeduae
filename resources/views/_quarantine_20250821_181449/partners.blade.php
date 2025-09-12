@extends('layouts.app')

@section('title', __('Partners'))
@section('content')
@include('partials.page_header', ['title' => __('Our Partners'), 'subtitle' => __('Organizations working with SawaedUAE')])

<div class="container my-4">
  <div class="row g-3">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <p class="mb-0">{{ __('Partner logos and descriptions will be shown here.') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
