<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Dompdf\Dompdf;

class CertificatesController extends Controller
{
    public function index(Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);

        $issuedCount = DB::table('certificates')->where('opportunity_id', $opportunity->id)->count();

        $eligibleApproved = DB::table('applications')
            ->where('opportunity_id',$opportunity->id)->where('status','approved')->count();

        $eligibleAttended = DB::table('attendances')
            ->where('opportunity_id',$opportunity->id)->whereNotNull('check_out_at')->count();

        return view('org.certificates.index', compact('opportunity','issuedCount','eligibleApproved','eligibleAttended'));
    }

    public function issue(Request $request, Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);

        $mode = $request->validate(['mode' => 'required|in:approved,attended'])['mode'];

        $userIds = $mode === 'approved'
            ? DB::table('applications')->where('opportunity_id',$opportunity->id)->where('status','approved')->pluck('user_id')->unique()
            : DB::table('attendances')->where('opportunity_id',$opportunity->id)->whereNotNull('check_out_at')->pluck('user_id')->unique();

        $done = 0;
        foreach ($userIds as $uid) {
            // Skip if already issued
            $exists = DB::table('certificates')->where(['opportunity_id'=>$opportunity->id,'user_id'=>$uid])->exists();
            if ($exists) continue;

            $minutes = (int) DB::table('attendances')->where(['opportunity_id'=>$opportunity->id,'user_id'=>$uid])->sum('minutes');
            $hours = round($minutes / 60, 2);

            $code = $this->generateCode();
            $verifyUrl = url('/verify/'.$code);
            $pdfPath   = public_path('certificates');
            if (!is_dir($pdfPath)) @mkdir($pdfPath, 0755, true);
            $filename  = $code.'.pdf';
            $absFile   = $pdfPath.'/'.$filename;

            // Build simple certificate HTML
            $user = DB::table('users')->where('id',$uid)->first();
            $orgName = DB::table('organizations')->where('id',$opportunity->organization_id)->value('name') ?? config('app.name');
            $qrSvg = QrCode::format('svg')->size(160)->generate($verifyUrl);

            $html = view('org.certificates._pdf', [
                'user'=>$user,
                'opportunity'=>$opportunity,
                'orgName'=>$orgName,
                'hours'=>$hours,
                'minutes'=>$minutes,
                'code'=>$code,
                'verifyUrl'=>$verifyUrl,
                'qrSvg'=>$qrSvg,
            ])->render();

            // Render PDF
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            file_put_contents($absFile, $dompdf->output());

            // Insert certificate row
            $now = now();
            $row = [
                'user_id'          => $uid,
                'opportunity_id'   => $opportunity->id,
                'code'             => $code,
                'verification_code'=> $code,
                'file_path'        => '/certificates/'.$filename,
                'hours'            => $hours,
                'issued_at'        => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
            if (Schema::hasColumn('certificates','certificate_number')) $row['certificate_number'] = $code;
            DB::table('certificates')->insert($row);

            $done++;
        }

        DB::table('audit_logs')->insert([
            'actor_id'    => Auth::id(),
            'action'      => 'certificates.issue_bulk',
            'entity_type' => 'opportunity',
            'entity_id'   => $opportunity->id,
            'note'        => json_encode(['mode'=>$mode,'count'=>$done]),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('status', __('Issued :n certificates.', ['n'=>$done]));
    }

    public function resend(Request $request, Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);

        $ids = $request->validate(['certificate_ids'=>'required|array','certificate_ids.*'=>'integer'])['certificate_ids'];
        $certs = DB::table('certificates')->where('opportunity_id',$opportunity->id)->whereIn('id',$ids)->get();

        foreach ($certs as $c) {
            $user = DB::table('users')->where('id',$c->user_id)->first();
            if (!$user || !$user->email) continue;
            $link = url($c->file_path);
            $verify = url('/verify/'.$c->code);
            try {
                \Mail::raw(
                    __("Your certificate is ready:\n:link\nVerify: :verify", ['link'=>$link,'verify'=>$verify]),
                    function($m) use ($user) { $m->to($user->email)->subject(__('Your volunteer certificate')); }
                );
            } catch (\Throwable $e) { /* ignore */ }
        }

        return back()->with('status', __('Resent :n emails.', ['n'=>$certs->count()]));
    }

    public function exportCsv(Opportunity $opportunity)
    {
        $this->authorize('manage', $opportunity);

        $rows = DB::table('certificates as c')
            ->join('users as u','u.id','=','c.user_id')
            ->selectRaw('c.id, c.code, u.name as user_name, u.email, c.hours, c.issued_at, c.file_path')
            ->where('c.opportunity_id', $opportunity->id)
            ->orderByDesc('c.created_at')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="certificates-'.$opportunity->id.'.csv"',
        ];

        return new StreamedResponse(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Code','Name','Email','Hours','Issued At','File','Verify URL']);
            foreach ($rows as $r) {
                fputcsv($out, [(string)$r->id, $r->code, $r->user_name, $r->email, (string)$r->hours, (string)$r->issued_at, $r->file_path, url('/verify/'.$r->code)]);
            }
            fclose($out);
        }, 200, $headers);
    }

    private function generateCode(): string
    {
        $date = now()->format('ymd');
        $rand = strtoupper(Str::random(6));
        return 'SU'.$date.'-'.$rand;
    }
}
