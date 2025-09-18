@if(session('status'))
  <div class="alert alert-success shadow-sm">{{ session('status') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger shadow-sm">
    <ul class="m-0">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif
