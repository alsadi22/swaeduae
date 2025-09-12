@extends('layouts.app')@section('title
@sec
<d
  <

  <a href="{{ route('events.index') }}" class="btn btn-outline-dark">{{ __('messages.browse_
</div

<div class="row g-3 mb-4">  <div class="col-
    <h4 class="mb-3">{{ __('messages.upco
    @if($upcoming->isEmpty())      <d

      <div class="row g-3">        
          <div class="col-12 col
            <div class="card p-3">              <h6 class="mb-1"><a hr
              <div class=
            </div>          </div>        @endforeach      </div>    @e
  </div></div><div>  <h4 class="mb-3">{{ __('messages.latest_events') }}</h4>  <div class="row g-3">    @foreach($latest as $e)      <div class="col-12 col-md-6 col-lg-4">        <div class="card p-3">          <h6 class="mb-1"><a href="{{ route('events.show',$e) }}">{{ $e->title }}</a></h6>          <div class="text-muted small">{{ $e->date->toDateString() }} — {{ $e->location }}</div>        </div>      </div>    @endforeach  </div></div
@endsectionEOF# Events indexcat > resources/views/events/index.blade.php <<'EOF'@extends('layouts.app')@section('title', __('messages.events'))@section('content')<form class="mb-3 d-flex" method="GET">  <input class="form-control me-2" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_events') }}">  <button class="btn b
</form><div class="row 
  @forelse($events as $e)    <div class="col-12 col-md-6 col-lg-4">      
        <h6 class="mb-1"><a href="{{ route('events.show',$e) }}">{{ $e->title }}</a><
        <div class="text-muted small mb-2">{{ $e->date->toDateString() }} —
        <p class="text-muted small mb-0">{{ \Illuminate
      </div>    </div>  @empty    <div class="col-12 text-muted">{{ __(
  @endforelse</div><div class=
  {{ $event
</div
@endsectionEOF# Event showcat 
@extends('layouts.app')@section('title', $ev
@sec
<d
  <

  <div class="mb-4">{!! nl2br(e($event->
  @auth    <a hre
  @else    <a href="{{ route('login') }
  @endauth</div>@endsectionEOF# Volunteer profilecat > resources/views/volunt

@section('title', __('messages.dashboard'))@section
<div class="row g-3">  <div class="col-lg-4">    
      <h5 class
      <div class="text-muted small mb-2">{{ auth()->user()->email }}</div>      <div class="f
    </
  </div>  <div class="col
    <div class="card p-3 mb-3">      <h6 class="mb-3">{{ __('messages.r
      @if($hours->isEmpty())        <div class
      @else        <div class="table-responsive">          <table class="table table-sm">            <t
           
              @foreach($hours as $h)
                  <td>{{ $h->date->toDateString() }}</td>                
                  <td>{{ $h->status }}
                  <td>{{ $h->notes }}</td>   
            
       
      
        </div>      @endif    </div>    <div class="card
      <h6 class="mb-3">{{
      @if($certificates->isEmpty())        <div class="text-muted small">{{ __('messa

        <ul class="list-group">          @foreach($certificates as $c)            <li class="li
              <span>#{{ $c->id }} — {{ $c->st
             

          @endforeach        </ul>      @endif    </div>  </div></div>@endsectionEOF# Certificate verificationcat > resources/views/certificates/verify.blade.php <<'EOF'@extends('layouts.app')@section('title', __('messages.certificate_verification'))@section('content')<div class="card p-4 text-center">  @if(!$found)     <h4 class="text-danger mb-2">{{ __('messages.certificate_not_found') }}</h4>     <p class="text-muted">{{ __('messages.certificate_not_found_desc') }}</p>  @else     <h4 class="text-success mb-2">{{ __('messages.certificate_valid') }}</h4>     <p class="text-muted">{{ __('messages.certificate_valid_desc') }}</p>     <div class="mt-3">        <div class="fw-bold">ID: {{ $certificate-
        <div>COD
        <div
   
     </d
  @end

@endsectionEOF# Static pag
cat 
@extend

@section('content')<div class="card p-4"><h3 

EOFcat > resources/views/pages
@extend
@se
@se


  <p class="text-muted">{{ __('mes
  <ul class="mb-0">
    <li>Email: info@swaeduae.ae</li>
  </ul>
</div>
@endsection
EOF

cat > resources/views/pages/faq.blade.php <<'EOF'
@extends('layouts.app')
@section('title', __('messages.faq'))
@section('content')
<div class="card p-4">
  <h3 class="mb-3">{{ __('messages.faq') }}</h3>
  <p class="text-muted">{{ __('messages.faq_text') }}</p>
</div>
@endsection
EOF

cat > resources/views/pages/platform.blade.php <<'EOF'
@extends('layouts.app')
@section('title', __('messages.platform'))
@section('content')
<div class="card p-4">
  <h3 class="mb-3">{{ __('messages.platform') }}</h3>
  <p class="text-muted">{{ __('messages.platform_text') }}</p>
</div>
@endsection
EOF

# Admin Events (simple)
cat > resources/views/admin/events/index.blade.php <<'EOF'
@extends('layouts.app')
@section('title','Admin — Events')

@section('content')
<a class="btn btn-dark mb-3" href="{{ route('admin.events.create') }}">+ New Event</a>
<div class="table-responsive">
<table class="table">
  <thead><tr><th>ID</th><th>Title</th><th>Date</th><th>Location</th><th></th></tr></thead>
  <tbody>
    @foreach($events as $e)
      <tr>
        <td>{{ $e->id }}</td>
        <td>{{ $e->title }}</td>
        <td>{{ $e->date->toDateString() }}</td>
        <td>{{ $e->location }}</td>
        <td class="text-nowrap">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.events.edit',$e) }}">Edit</a>
          <form class="d-inline" method="POST" action="{{ route('admin.events.destroy',$e) }}">@csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
          </form>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
</div>
{{ $events->links() }}
@endsection
EOF

cat > resources/views/admin/events/create.blade.php <<'EOF'
@extends('layouts.app')
@section('title','Admin — Create Event')

@section('content')
<form method="POST" action="{{ route('admin.events.store') }}" class="card p-4">
  @csrf
  @include('admin.events.form', ['button' => 'Create'])
</form>
@endsection
EOF

cat > resources/views/admin/events/edit.blade.php <<'EOF'
@extends('layouts.app')
@section('title','Admin — Edit Event')

@section('content')
<form method="POST" action="{{ route('admin.events.update',$event) }}" class="card p-4">
  @csrf @method('PUT')
  @include('admin.events.form', ['button' => 'Update'])
</form>
@endsection
EOF

cat > resources/views/admin/events/form.blade.php <<'EOF'
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Title</label>
    <input class="form-control" name="title" value="{{ old('title', $event->title ?? '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Date</label>
    <input type="date" class="form-control" name="date" value="{{ old('date', isset($event) ? $event->date->toDateString() : '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Location</label>
    <input class="form-control" name="location" value="{{ old('location', $event->location ?? '') }}">
  </div>
  <div class="col-12">
    <label class="form-label">Description</label>
    <textarea rows="6" class="form-control" name="description">{{ old('description', $event->description ?? '') }}</textarea>
  </div>
  <div class="col-md-4">
    <label class="form-label">Hours</label>
    <input type="number" step="0.25" class="form-control" name="hours" value="{{ old('hours', $event->hours ?? '') }}">
  </div>
  <div class="col-12">
    <button class="btn btn-dark">{{ $button }}</button>
  </div>
</div>
EOF

########################################
# 7) TRANSLATION KEYS (append if files exist)
########################################
mkdir -p resources/lang/ar resources/lang/en

# We append arrays safely; adjust if your files already return arrays.
cat > resources/lang/en/messages.php <<'EOF'
<?php
return [
  'events' => 'Events',
  'platform' => 'The Platform',
  'faq' => 'FAQ',
  'contact' => 'Contact',
  'dashboard' => 'Dashboard',
  'logout' => 'Logout',
  'login' => 'Login',
  'register' => 'Register',
  'make_difference' => 'Make a Difference',
  'join_thousands' => 'Join thousands of volunteers serving the community.',
  'browse_events' => 'Browse Events',
  'upcoming_opportunities' => 'Upcoming Opportunities',
  'no_opportunities' => 'No opportunities available.',
  'latest_events' => 'Latest Events',
  'search_events' => 'Search events...',
  'search' => 'Search',
  'no_events' => 'No events found.',
  'join_event' => 'Join this Event',
  'login_to_join' => 'Login to Join',
  'total_hours' => 'Total Hours',
  'recent_hours' => 'Recent Hours',
  'no_hours' => 'No hours yet.',
  'hours' => 'Hours',
  'date' => 'Date',
  'status' => 'Status',
  'notes' => 'Notes',
  'certificates' => 'Certificates',
  'no_certificates' => 'No certificates yet.',
  'view' => 'View',
  'certificate_verification' => 'Certificate Verification',
  'certificate_not_found' => 'Certificate Not Found',
  'certificate_not_found_desc' => 'We could not find a certificate with this code.',
  'certificate_valid' => 'Certificate is Valid',
  'certificate_valid_desc' => 'This certificate is valid in the SawaedUAE system.',
  'issued_at' => 'Issued at',
  'about' => 'About',
  'about_text' => 'Sawaed UAE is a volunteer platform connecting people to community impact.',
  'contact_text' => 'Reach us with any questions or partnership ideas.',
  'platform_text' => 'Learn about how our platform works and how to participate.',
];
EOF

cat > resources/lang/ar/messages.php <<'EOF'
<?php
return [
  'events' => 'الفعاليات',
  'platform' => 'عن المنصة',
  'faq' => 'الأسئلة الشائعة',
  'contact' => 'تواصل معنا',
  'dashboard' => 'لوحة التحكم',
  'logout' => 'تسجيل الخروج',
  'login' => 'تسجيل الدخول',
  'register' => 'إنشاء حساب',
  'make_difference' => 'اصنع الفرق',
  'join_thousands' => 'انضم إلى آلاف المتطوعين الذين يساهمون في خدمة المجتمع.',
  'browse_events' => 'استكشف الفعاليات',
  'upcoming_opportunities' => 'الفرص القادمة',
  'no_opportunities' => 'لا توجد فرص متاحة.',
  'latest_events' => 'أحدث الفعاليات',
  'search_events' => 'ابحث في الفعاليات...',
  'search' => 'بحث',
  'no_events' => 'لا توجد فعاليات.',
  'join_event' => 'انضم إلى هذه الفعالية',
  'login_to_join' => 'سجّل الدخول للانضمام',
  'total_hours' => 'إجمالي الساعات',
  'recent_hours' => 'أحدث الساعات',
  'no_hours' => 'لا توجد ساعات مسجلة.',
  'hours' => 'الساعات',
  'date' => 'التاريخ',
  'status' => 'الحالة',
  'notes' => 'ملاحظات',
  'certificates' => 'الشهادات',
  'no_certificates' => 'لا توجد شهادات بعد.',
  'view' => 'عرض',
  'certificate_verification' => 'التحقق من الشهادة',
  'certificate_not_found' => 'لم يتم العثور على الشهادة',
  'certificate_not_found_desc' => 'لم نتمكن من العثور على شهادة بهذا الرمز.',
  'certificate_valid' => 'الشهادة صالحة',
  'certificate_valid_desc' => 'هذه الشهادة صالحة في نظام سواعد الإمارات.',
  'issued_at' => 'تاريخ الإصدار',
  'about' => 'من نحن',
  'about_text' => 'سواعد الإمارات منصة للتطوع تربط الأفراد بفرص خدمة المجتمع.',
  'contact_text' => 'تواصل معنا لأي استفسارات أو شراكات.',
  'platform_text' => 'تعرّف على كيفية عمل منصتنا وكيفية المشاركة.',
];
EOF

########################################
# 8) RUN MIGRATIONS
########################################
php artisan migrate --force
echo "✅ All set. If you need seed data, create a few events in Admin > Events."

