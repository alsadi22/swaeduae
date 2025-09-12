#!/usr/bin/env bash
set -euo pipefail

ROOT="/home3/vminingc/swaeduae.ae/laravel-app"
BACKUP_DIR="/home3/vminingc/backups/sawaeduae"
TIMESTAMP="$(date +%Y%m%d_%H%M%S)"

echo "==> Backup starting..."
mkdir -p "$BACKUP_DIR"
php -v || true
php artisan route:list > "$BACKUP_DIR/routes_$TIMESTAMP.txt" || true
mysqldump --user=vminingc_admin --no-tablespaces --single-transaction vminingc_swaeduae_db > "$BACKUP_DIR/db_$TIMESTAMP.sql" || true
zip -r "$BACKUP_DIR/swaeduae2025x_$TIMESTAMP.zip" "$ROOT" -x "$ROOT/vendor/*" "$ROOT/storage/logs/*" "$ROOT/node_modules/*" || true
echo "==> Backup saved: $BACKUP_DIR/swaeduae2025x_$TIMESTAMP.zip"

echo "==> Backing up routes..."
cp -f routes/web.php "routes/web.php.$TIMESTAMP.bak" || true
[ -f routes/admin.php ] && cp -f routes/admin.php "routes/admin.php.$TIMESTAMP.bak" || true

echo "==> Creating migrations (certificates & attendance enhancements)..."
php artisan make:migration enhance_certificates_table_add_columns_issued_at_file_hours --table=certificates || true
cat > database/migrations/$(ls -1t database/migrations | head -n1) <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'hours')) {
                $table->decimal('hours', 6, 2)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('certificates', 'file_path')) {
                $table->string('file_path')->nullable()->after('hours');
            }
            if (!Schema::hasColumn('certificates', 'code')) {
                $table->string('code', 32)->unique()->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('certificates', 'issued_at')) {
                $table->timestamp('issued_at')->nullable()->after('code');
            }
        });
    }
    public function down(): void {
        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'hours')) $table->dropColumn('hours');
            if (Schema::hasColumn('certificates', 'file_path')) $table->dropColumn('file_path');
            if (Schema::hasColumn('certificates', 'code')) $table->dropColumn('code');
            if (Schema::hasColumn('certificates', 'issued_at')) $table->dropColumn('issued_at');
        });
    }
};
PHP

php artisan make:migration enhance_attendance_add_minutes_no_show_notes --table=attendances || true
cat > database/migrations/$(ls -1t database/migrations | head -n1) <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'minutes')) {
                $table->integer('minutes')->nullable()->after('check_out_at');
            }
            if (!Schema::hasColumn('attendances', 'no_show')) {
                $table->boolean('no_show')->default(false)->after('minutes');
            }
            if (!Schema::hasColumn('attendances', 'notes')) {
                $table->text('notes')->nullable()->after('no_show');
            }
        });
    }
    public function down(): void {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'minutes')) $table->dropColumn('minutes');
            if (Schema::hasColumn('attendances', 'no_show')) $table->dropColumn('no_show');
            if (Schema::hasColumn('attendances', 'notes')) $table->dropColumn('notes');
        });
    }
};
PHP

echo "==> Adding Services & Mailables..."
mkdir -p app/Services app/Mail resources/views/emails
cat > app/Services/CertificateService.php <<'PHP'
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Certificate;
use App\Models\Opportunity;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateIssued;

class CertificateService
{
    public function generate(User $user, Opportunity $opportunity, float $hours): Certificate
    {
        // Unique code
        do {
            $code = Str::upper(Str::random(10));
        } while (Certificate::where('code', $code)->exists());

        $verifyUrl = url('/verify/'.$code);

        // QR (PNG base64)
        $qrPng = QrCode::format('png')->size(240)->margin(1)->generate($verifyUrl);
        $qrBase64 = 'data:image/png;base64,'.base64_encode($qrPng);

        // PDF view data
        $data = [
            'user'       => $user,
            'opportunity'=> $opportunity,
            'hours'      => $hours,
            'code'       => $code,
            'verifyUrl'  => $verifyUrl,
            'qrBase64'   => $qrBase64,
            'issuedAt'   => Carbon::now()->timezone(config('app.timezone')),
        ];

        $pdf = Pdf::loadView('certificates.template', $data)->setPaper('A4', 'landscape');

        // Storage path
        $dir = 'certificates/'.date('Y').'/'.date('m');
        $filename = $code.'.pdf';
        $path = $dir.'/'.$filename;

        Storage::makeDirectory($dir);
        Storage::put($path, $pdf->output());

        $cert = new Certificate();
        $cert->user_id    = $user->id;
        $cert->event_id   = $opportunity->id;
        $cert->hours      = $hours;
        $cert->file_path  = $path;
        $cert->code       = $code;
        $cert->issued_at  = Carbon::now();
        $cert->save();

        return $cert;
    }

    public function sendEmail(Certificate $certificate): void
    {
        try {
            $user = $certificate->user;
            Mail::to($user->email)->send(new CertificateIssued($certificate));
        } catch (\Throwable $e) {
            Log::error('Certificate email send failed: '.$e->getMessage());
        }
    }

    /**
     * Returns a WhatsApp share URL pre-filled with a message.
     * Replace with provider API integration when ready.
     */
    public function whatsappShareLink(Certificate $certificate): string
    {
        $user = $certificate->user;
        $verifyUrl = url('/verify/'.$certificate->code);
        $downloadUrl = url('/storage/'.$certificate->file_path);
        $msg = "السلام عليكم ${user->name}،\nشهادتك التطوعية جاهزة ✅\nتنزيل: $downloadUrl\nالتحقق: $verifyUrl\nشكراً لمساهمتك!";
        $encoded = urlencode($msg);
        return "https://wa.me/?text={$encoded}";
    }
}
PHP

cat > app/Mail/CertificateIssued.php <<'PHP'
<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateIssued extends Mailable
{
    use Queueable, SerializesModels;

    public Certificate $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function build()
    {
        $downloadUrl = url('/storage/'.$this->certificate->file_path);
        $verifyUrl   = url('/verify/'.$this->certificate->code);

        return $this->subject(__('messages.certificate_subject', ['event' => $this->certificate->event->title ?? '']))
            ->view('emails.certificate_issued', [
                'certificate' => $this->certificate,
                'downloadUrl' => $downloadUrl,
                'verifyUrl'   => $verifyUrl,
            ]);
    }
}
PHP

cat > resources/views/emails/certificate_issued.blade.php <<'BLADE'
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"><title>{{ __('messages.certificate_email_title') }}</title></head>
<body style="font-family:Arial,Helvetica,sans-serif;">
  <h2>{{ __('messages.congratulations') }}, {{ $certificate->user->name }}!</h2>
  <p>{{ __('messages.certificate_ready_for', ['event' => $certificate->event->title ?? '']) }}</p>
  <p>
    <a href="{{ $downloadUrl }}">{{ __('messages.download_certificate') }}</a> |
    <a href="{{ $verifyUrl }}">{{ __('messages.verify_certificate') }}</a>
  </p>
  <p>{{ __('messages.thank_you_volunteering') }}</p>
</body>
</html>
BLADE

echo "==> Admin Attendance Manager controller & view..."
mkdir -p app/Http/Controllers/Admin resources/views/admin/attendance
cat > app/Http/Controllers/Admin/AttendanceController.php <<'PHP'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Opportunity;
use App\Models\User;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Opportunity $opportunity)
    {
        $this->authorize('admin'); // Gate via middleware; extra guard if policy exists
        $attendances = Attendance::with('user')
            ->where('opportunity_id', $opportunity->id)
            ->orderByDesc('id')
            ->paginate(50);

        return view('admin.attendance.index', compact('opportunity', 'attendances'));
    }

    public function update(Request $request, Opportunity $opportunity, Attendance $attendance)
    {
        $request->validate([
            'minutes'  => 'nullable|integer|min:0|max:100000',
            'no_show'  => 'nullable|boolean',
            'notes'    => 'nullable|string|max:5000',
            'check_in_at'  => 'nullable|date',
            'check_out_at' => 'nullable|date|after_or_equal:check_in_at',
        ]);

        $attendance->minutes     = $request->filled('minutes') ? (int)$request->minutes : $attendance->minutes;
        $attendance->no_show     = (bool)$request->input('no_show', $attendance->no_show);
        if ($request->filled('notes')) $attendance->notes = $request->notes;
        if ($request->filled('check_in_at'))  $attendance->check_in_at  = Carbon::parse($request->check_in_at);
        if ($request->filled('check_out_at')) $attendance->check_out_at = Carbon::parse($request->check_out_at);
        if (!$attendance->minutes && $attendance->check_in_at && $attendance->check_out_at) {
            $attendance->minutes = $attendance->check_in_at->diffInMinutes($attendance->check_out_at);
        }
        $attendance->save();

        // Recalculate user's total minutes (simple sum of attendance)
        $totalMinutes = Attendance::where('user_id', $attendance->user_id)->whereNull('deleted_at')->sum('minutes');
        DB::table('users')->where('id', $attendance->user_id)->update(['total_minutes_cached' => $totalMinutes]); // optional cache field

        return back()->with('status', __('messages.attendance_updated'));
    }

    public function finalizeIssue(Opportunity $opportunity, CertificateService $certs)
    {
        // Issue certificates for completed (non no-show) attendances
        $issued = 0;
        $attendances = Attendance::with('user')
            ->where('opportunity_id', $opportunity->id)
            ->where('no_show', false)
            ->get();

        foreach ($attendances as $a) {
            if (!$a->user) continue;

            $minutes = $a->minutes;
            if (!$minutes && $a->check_in_at && $a->check_out_at) {
                $minutes = $a->check_in_at->diffInMinutes($a->check_out_at);
            }
            if (!$minutes || $minutes <= 0) continue;

            $hours = round($minutes / 60, 2);

            // Skip if a certificate for this user+event already exists
            $found = Certificate::where('user_id', $a->user_id)->where('event_id', $opportunity->id)->first();
            if ($found) continue;

            $cert = $certs->generate($a->user, $opportunity, $hours);
            $certs->sendEmail($cert);
            $issued++;
        }

        return back()->with('status', __('messages.certificates_issued', ['count' => $issued]));
    }

    public function resendCertificate(Certificate $certificate, CertificateService $certs)
    {
        $certs->sendEmail($certificate);
        return back()->with('status', __('messages.certificate_resent'));
    }
}
PHP

cat > resources/views/admin/attendance/index.blade.php <<'BLADE'
@extends('admin.layout')

@section('content')
<div class="container-fluid">
  <h1 class="mt-3 mb-3">{{ __('messages.attendance_manager') }} — {{ $opportunity->title }}</h1>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="mb-3">
    <form action="{{ route('admin.opportunities.finalize.issue', $opportunity) }}" method="POST" onsubmit="return confirm('{{ __('messages.issue_confirm') }}')">
      @csrf
      <button class="btn btn-primary">{{ __('messages.issue_certificates_now') }}</button>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('messages.volunteer') }}</th>
          <th>{{ __('messages.check_in') }}</th>
          <th>{{ __('messages.check_out') }}</th>
          <th>{{ __('messages.minutes') }}</th>
          <th>{{ __('messages.no_show') }}</th>
          <th>{{ __('messages.notes') }}</th>
          <th>{{ __('messages.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($attendances as $a)
          <tr>
            <td>{{ $a->id }}</td>
            <td>{{ $a->user?->name }}</td>
            <form method="POST" action="{{ route('admin.opportunities.attendance.update', [$opportunity, $a]) }}">
              @csrf
              <td><input type="datetime-local" name="check_in_at" class="form-control form-control-sm" value="{{ $a->check_in_at ? $a->check_in_at->format('Y-m-d\TH:i') : '' }}"></td>
              <td><input type="datetime-local" name="check_out_at" class="form-control form-control-sm" value="{{ $a->check_out_at ? $a->check_out_at->format('Y-m-d\TH:i') : '' }}"></td>
              <td style="max-width:120px"><input type="number" name="minutes" class="form-control form-control-sm" min="0" value="{{ $a->minutes }}"></td>
              <td><input type="checkbox" name="no_show" value="1" {{ $a->no_show ? 'checked' : '' }}></td>
              <td><input type="text" name="notes" class="form-control form-control-sm" value="{{ $a->notes }}"></td>
              <td><button class="btn btn-sm btn-success">{{ __('messages.save') }}</button></td>
            </form>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $attendances->links() }}
</div>
@endsection
BLADE

echo "==> Volunteer Profile (tabs) controller & view..."
mkdir -p app/Http/Controllers/Volunteer resources/views/volunteer
cat > app/Http/Controllers/Volunteer/ProfileController.php <<'PHP'
<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Opportunity;
use App\Models\Certificate;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(Request $request, string $tab = 'overview')
    {
        $user = Auth::user();
        $tab = in_array($tab, ['overview','hours','events','applications','certificates']) ? $tab : 'overview';

        $totalMinutes = Attendance::where('user_id', $user->id)->sum('minutes');
        if (!$totalMinutes) $totalMinutes = 0;
        $totalHours = round($totalMinutes / 60, 2);

        $events = Attendance::with('opportunity')
            ->where('user_id', $user->id)->orderByDesc('id')->paginate(10);

        $applications = DB::table('applications')
            ->where('user_id', $user->id)->orderByDesc('id')->paginate(10);

        $certificates = Certificate::with('event')
            ->where('user_id', $user->id)->orderByDesc('issued_at')->paginate(10);

        return view('volunteer.profile', compact('user', 'tab', 'totalHours', 'events', 'applications', 'certificates'));
    }
}
PHP

cat > resources/views/volunteer/profile.blade.php <<'BLADE'
@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="mb-3">{{ __('messages.my_profile') }}</h1>

  <div class="row mb-3">
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title">{{ $user->name }}</h5>
          <p class="mb-1">{{ $user->email }}</p>
          <p class="mb-0">{{ __('messages.total_hours') }}: <strong>{{ $totalHours }}</strong></p>
        </div>
      </div>
    </div>
  </div>

  <ul class="nav nav-tabs mb-3">
    @php $tabs = ['overview'=>__('messages.overview'), 'hours'=>__('messages.hours'), 'events'=>__('messages.events_attended'), 'applications'=>__('messages.applications'), 'certificates'=>__('messages.certificates')]; @endphp
    @foreach($tabs as $key=>$label)
      <li class="nav-item">
        <a class="nav-link {{ $tab === $key ? 'active' : '' }}" href="{{ route('volunteer.profile', ['tab'=>$key]) }}">{{ $label }}</a>
      </li>
    @endforeach
  </ul>

  @if($tab === 'overview' || $tab === 'hours')
    <div class="card mb-3">
      <div class="card-header">{{ __('messages.hours') }}</div>
      <div class="card-body">
        <p>{{ __('messages.total_hours') }}: <strong>{{ $totalHours }}</strong></p>
      </div>
    </div>
  @endif

  @if($tab === 'overview' || $tab === 'events')
    <div class="card mb-3">
      <div class="card-header">{{ __('messages.events_attended') }}</div>
      <div class="card-body table-responsive">
        <table class="table table-sm">
          <thead><tr><th>#</th><th>{{ __('messages.event') }}</th><th>{{ __('messages.minutes') }}</th><th>{{ __('messages.checked_in') }}</th><th>{{ __('messages.checked_out') }}</th></tr></thead>
          <tbody>
            @foreach($events as $e)
              <tr>
                <td>{{ $e->id }}</td>
                <td>{{ $e->opportunity?->title }}</td>
                <td>{{ $e->minutes }}</td>
                <td>{{ $e->check_in_at }}</td>
                <td>{{ $e->check_out_at }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{ $events->links() }}
      </div>
    </div>
  @endif

  @if($tab === 'overview' || $tab === 'applications')
    <div class="card mb-3">
      <div class="card-header">{{ __('messages.applications') }}</div>
      <div class="card-body table-responsive">
        <table class="table table-sm">
          <thead><tr><th>#</th><th>{{ __('messages.event') }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.applied_at') }}</th></tr></thead>
          <tbody>
            @foreach($applications as $a)
              <tr>
                <td>{{ $a->id }}</td>
                <td>{{ $a->opportunity_id }}</td>
                <td>{{ $a->status }}</td>
                <td>{{ $a->created_at }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{ $applications->links() }}
      </div>
    </div>
  @endif

  @if($tab === 'overview' || $tab === 'certificates')
    <div class="card mb-3">
      <div class="card-header">{{ __('messages.certificates') }}</div>
      <div class="card-body table-responsive">
        <table class="table table-sm">
          <thead><tr><th>#</th><th>{{ __('messages.event') }}</th><th>{{ __('messages.hours') }}</th><th>{{ __('messages.issued_at') }}</th><th>{{ __('messages.actions') }}</th></tr></thead>
          <tbody>
            @foreach($certificates as $c)
              <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->event?->title }}</td>
                <td>{{ $c->hours }}</td>
                <td>{{ $c->issued_at }}</td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="{{ url('/storage/'.$c->file_path) }}" target="_blank">{{ __('messages.download') }}</a>
                  <a class="btn btn-sm btn-outline-secondary" href="{{ url('/verify/'.$c->code) }}" target="_blank">{{ __('messages.verify') }}</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{ $certificates->links() }}
      </div>
    </div>
  @endif

</div>
@endsection
BLADE

echo "==> Certificate PDF template..."
mkdir -p resources/views/certificates
cat > resources/views/certificates/template.blade.php <<'BLADE'
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; background: #FBF3D5; color:#333; }
    .wrap { padding: 40px; border: 10px solid #D6A99D; }
    h1 { font-size: 32px; margin: 0 0 10px; }
    .muted { color:#555; }
    .row { display:flex; justify-content:space-between; align-items:center; margin-top:25px; }
    .box { background:#fff; border:1px solid #ddd; padding:20px; border-radius:12px; }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>شهادة شكر وتقدير</h1>
    <p class="muted">Certificate of Appreciation</p>
    <hr>
    <p>نُقَدِّم هذه الشهادة إلى</p>
    <h2>{{ $user->name }}</h2>
    <p>لمساهمته التطوعية في فعالية: <strong>{{ $opportunity->title }}</strong></p>
    <p>عدد الساعات: <strong>{{ number_format($hours, 2) }}</strong></p>
    <div class="row">
      <div class="box">
        <p>التحقق: {{ $verifyUrl }}</p>
        <p>الكود: <strong>{{ $code }}</strong></p>
        <p>تاريخ الإصدار: {{ $issuedAt }}</p>
      </div>
      <div>
        <img src="{{ $qrBase64 }}" alt="QR" width="180" height="180">
      </div>
    </div>
  </div>
</body>
</html>
BLADE

echo "==> Routes (admin & web) additions..."
# Ensure use statements in routes/admin.php
if [ -f routes/admin.php ]; then
  if ! grep -q "use App\\Http\\Controllers\\Admin\\AttendanceController;" routes/admin.php; then
    sed -i "1i <?php\nuse App\\Http\\Controllers\\Admin\\AttendanceController;\n" routes/admin.php
  fi
else
  echo "<?php\nuse Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\Admin\\AttendanceController;\n" > routes/admin.php
fi

# Append route group safely (unique route names)
cat >> routes/admin.php <<'PHP'

Route::middleware(['web','auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/opportunities/{opportunity}/attendance', [AttendanceController::class, 'index'])->name('opportunities.attendance');
    Route::post('/opportunities/{opportunity}/attendance/{attendance}', [AttendanceController::class, 'update'])->name('opportunities.attendance.update');
    Route::post('/opportunities/{opportunity}/finalize-issue', [AttendanceController::class, 'finalizeIssue'])->name('opportunities.finalize.issue');
    Route::post('/certificates/{certificate}/resend', [AttendanceController::class, 'resendCertificate'])->name('certificates.resend');
});
PHP

# Web route for volunteer profile tabs
if ! grep -q "use App\\Http\\Controllers\\Volunteer\\ProfileController;" routes/web.php; then
  sed -i "1i <?php\nuse App\\Http\\Controllers\\Volunteer\\ProfileController;\n" routes/web.php
fi

cat >> routes/web.php <<'PHP'

Route::middleware(['web','auth','verified'])->group(function () {
    Route::get('/volunteer/profile/{tab?}', [ProfileController::class, 'index'])
        ->where('tab', 'overview|hours|events|applications|certificates')
        ->name('volunteer.profile');
});
PHP

echo "==> Similar Opportunities partial & injection..."
mkdir -p resources/views/opportunities/partials
cat > resources/views/opportunities/partials/similar.blade.php <<'BLADE'
@php
  $similar = \App\Models\Opportunity::where('id','!=',$opportunity->id)
    ->when(isset($opportunity->category_id), fn($q)=>$q->where('category_id',$opportunity->category_id))
    ->latest()->take(3)->get();
@endphp
@if($similar->count())
<hr>
<h3 class="mt-3 mb-2">{{ __('messages.similar_opportunities') }}</h3>
<div class="row">
  @foreach($similar as $s)
    <div class="col-md-4 mb-3">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">{{ $s->title }}</h5>
          <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit($s->description, 120) }}</p>
          <a class="btn btn-outline-primary btn-sm" href="{{ route('opportunities.show', $s->id) }}">{{ __('messages.view') }}</a>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endif
BLADE

# Append include at end of show view if present and not already included
if [ -f resources/views/opportunities/show.blade.php ]; then
  if ! grep -q "opportunities.partials.similar" resources/views/opportunities/show.blade.php; then
    echo "@include('opportunities.partials.similar')" >> resources/views/opportunities/show.blade.php
  fi
fi

echo "==> i18n keys merge (EN & AR)..."
mkdir -p resources/lang/en resources/lang/ar

# English
cat >> resources/lang/en/messages.php <<'PHP'
<?php if (!isset($messages)) $messages = []; return array_replace_recursive($messages ?? [], [
  'certificate_subject' => 'Your volunteer certificate for :event',
  'certificate_email_title' => 'Volunteer Certificate',
  'congratulations' => 'Congratulations',
  'certificate_ready_for' => 'Your certificate is ready for :event',
  'download_certificate' => 'Download certificate',
  'verify_certificate' => 'Verify certificate',
  'thank_you_volunteering' => 'Thank you for volunteering!',
  'attendance_manager' => 'Attendance Manager',
  'issue_certificates_now' => 'Issue Certificates Now',
  'issue_confirm' => 'Issue certificates for all completed attendees?',
  'attendance_updated' => 'Attendance updated.',
  'certificates_issued' => ':count certificates issued.',
  'certificate_resent' => 'Certificate re-sent via email.',
  'volunteer' => 'Volunteer',
  'check_in' => 'Check-in',
  'check_out' => 'Check-out',
  'minutes' => 'Minutes',
  'no_show' => 'No-show',
  'notes' => 'Notes',
  'actions' => 'Actions',
  'save' => 'Save',
  'my_profile' => 'My Profile',
  'overview' => 'Overview',
  'hours' => 'Hours',
  'events_attended' => 'Events Attended',
  'applications' => 'Applications',
  'certificates' => 'Certificates',
  'total_hours' => 'Total Hours',
  'event' => 'Event',
  'status' => 'Status',
  'applied_at' => 'Applied at',
  'issued_at' => 'Issued at',
  'download' => 'Download',
  'verify' => 'Verify',
  'similar_opportunities' => 'Similar Opportunities',
  'view' => 'View',
]); 
PHP

# Arabic
cat >> resources/lang/ar/messages.php <<'PHP'
<?php if (!isset($messages)) $messages = []; return array_replace_recursive($messages ?? [], [
  'certificate_subject' => 'شهادتك التطوعية لفعالية :event',
  'certificate_email_title' => 'شهادة تطوع',
  'congratulations' => 'مبروك',
  'certificate_ready_for' => 'شهادتك جاهزة لفعالية :event',
  'download_certificate' => 'تنزيل الشهادة',
  'verify_certificate' => 'تحقق من الشهادة',
  'thank_you_volunteering' => 'شكراً لتطوعك!',
  'attendance_manager' => 'إدارة الحضور',
  'issue_certificates_now' => 'إصدار الشهادات الآن',
  'issue_confirm' => 'هل تريد إصدار شهادات لكل الحضور المكتملين؟',
  'attendance_updated' => 'تم تحديث الحضور.',
  'certificates_issued' => 'تم إصدار :count شهادة.',
  'certificate_resent' => 'تم إعادة إرسال الشهادة عبر البريد.',
  'volunteer' => 'المتطوع',
  'check_in' => 'تسجيل الدخول',
  'check_out' => 'تسجيل الخروج',
  'minutes' => 'الدقائق',
  'no_show' => 'لم يحضر',
  'notes' => 'ملاحظات',
  'actions' => 'إجراءات',
  'save' => 'حفظ',
  'my_profile' => 'ملفي الشخصي',
  'overview' => 'نظرة عامة',
  'hours' => 'الساعات',
  'events_attended' => 'الفعاليات التي حضرتها',
  'applications' => 'الطلبات',
  'certificates' => 'الشهادات',
  'total_hours' => 'إجمالي الساعات',
  'event' => 'الفعالية',
  'status' => 'الحالة',
  'applied_at' => 'تاريخ التقديم',
  'issued_at' => 'تاريخ الإصدار',
  'download' => 'تنزيل',
  'verify' => 'تحقق',
  'similar_opportunities' => 'فرص مشابهة',
  'view' => 'عرض',
]); 
PHP

echo "==> Storage link, migrate, and cache refresh..."
php artisan storage:link || true
php artisan migrate --force
php artisan view:clear && php artisan route:clear && php artisan config:clear && php artisan cache:clear
php artisan config:cache && php artisan route:cache

echo "==> Iteration A applied successfully."
