<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

$opId = (int)($argv[1] ?? 0);
if (!$opId) { fwrite(STDERR,"Usage: php scripts/generate_certs.php OPPORTUNITY_ID\n"); exit(1); }

$op = DB::table('opportunities')->where('id',$opId)->first();
if (!$op) { fwrite(STDERR,"Opportunity not found: $opId\n"); exit(1); }

$hours = DB::table('volunteer_hours')->where('opportunity_id',$opId)->where('minutes','>',0)->get();
if (!$hours->count()) { echo "No hours found for opportunity $opId\n"; exit(0); }

$count=0;
foreach ($hours as $h) {
  $user = DB::table('users')->where('id',$h->user_id)->first();
  if (!$user) continue;

  $exists = DB::table('certificates')->where(['user_id'=>$user->id,'opportunity_id'=>$opId])->whereNull('revoked_at')->first();
  if ($exists) continue;

  $code  = 'SU-'.strtoupper(bin2hex(random_bytes(3))).'-'.date('ymd');
  $verifyUrl = url('/verify/'.$code);

  // SVG QR
  $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->generate($verifyUrl);

  $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.template', [
      'code'=>$code, 'user'=>$user, 'op'=>$op, 'minutes'=>$h->minutes ?? 0, 'qrSvg'=>$qrSvg, 'issued_at'=>now(),
  ])->setPaper('a4');

  $relPath = 'certificates/'.$code.'.pdf';
  Storage::disk('public')->put($relPath, $pdf->output());

  $payload = [$code,$user->name ?? '',$op->title ?? '',(string)($h->minutes ?? 0),date('Y-m-d')];
  $checksum = hash_hmac('sha256', implode('|',$payload), config('app.key') ?? 'swaeduae');

  $extras = [];
  if (Schema::hasColumn('certificates','certificate_number')) $extras['certificate_number'] = $code;
  if (Schema::hasColumn('certificates','verification_code'))  $extras['verification_code']  = $code;
  if (Schema::hasColumn('certificates','issued_at'))          $extras['issued_at']          = now();
  if (Schema::hasColumn('certificates','issued_date'))        $extras['issued_date']        = now()->toDateString();
  if (Schema::hasColumn('certificates','status'))             $extras['status']             = 'valid';
  if (Schema::hasColumn('certificates','language'))           $extras['language']           = app()->getLocale() ?? 'en';
  if (Schema::hasColumn('certificates','issuer'))             $extras['issuer']             = 'SawaedUAE';

  DB::table('certificates')->insert(array_merge([
    'user_id'=>$user->id,
    'opportunity_id'=>$opId,
    'title'=>'Volunteer Certificate',
    'code'=>$code,
    'file_path'=>'storage/'.$relPath,
    'checksum'=>$checksum,
    'created_at'=>now(), 'updated_at'=>now()
  ], $extras));

  $count++;
}
echo "Generated $count certificate(s) for opportunity $opId\n";
