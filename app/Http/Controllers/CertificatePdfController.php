<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\CertificateIssued;

class CertificatePdfController extends Controller
{
    protected function findOwned(int $id)
    {
        $uid = Auth::id();
        $cert = DB::table('certificates')->where('id',$id)->where('user_id',$uid)->first();
        abort_unless($cert, 404);
        abort_if(!is_null($cert->revoked_at), 410, 'Certificate revoked');
        return $cert;
    }

    public function download(int $id)
    {
        $c = $this->findOwned($id);
        $user  = DB::table('users')->find($c->user_id);
        $event = DB::table('events')->find($c->event_id);

        $pdf = Pdf::loadView('certificates.pdf', ['c'=>$c,'user'=>$user,'event'=>$event])->setPaper('A4','landscape');

        $dir = 'public/certificates';
        Storage::makeDirectory($dir);
        $filename = ($c->code ?: ('CERT-'.$c->id)).'.pdf';
        Storage::put("$dir/$filename", $pdf->output());

        DB::table('certificates')->where('id',$c->id)->update([
            'pdf_path' => "storage/certificates/$filename",
            'updated_at' => now(),
        ]);

        return response()->streamDownload(function() use($pdf){ echo $pdf->output(); }, $filename, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    public function resend(int $id)
    {
        $c = $this->findOwned($id);
        $user = DB::table('users')->find($c->user_id);
        Mail::to($user->email)->queue(new CertificateIssued($c));
        return back()->with('status', "Resent certificate to {$user->email}");
    }

    public function revoke(int $id, Request $r)
    {
        $u = Auth::user();
        if (!$u || !method_exists($u,'hasRole') || !$u->hasRole('admin')) abort(403);
        DB::table('certificates')->where('id',$id)->update(['revoked_at'=>now(),'updated_at'=>now()]);
        return back()->with('status', 'Certificate revoked');
    }
}
