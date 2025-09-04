<?php
namespace App\Http\Controllers\Org;
use App\Http\Controllers\Controller;
use App\Mail\ApplicantDecisionMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmailPreviewController extends Controller
{
    public function show()
    {
        $orgId = DB::table('organizations')->where('owner_user_id', Auth::id())->value('id');
        $org = DB::table('organizations')->where('id',$orgId)->first();
        $orgName = $org->name ?? config('app.name');
        $brand = $org->primary_color ?? DB::table('settings')->where('key',"org:{$orgId}:primary_color")->value('value');
        $logo  = $org->logo_path ?? DB::table('settings')->where('key',"org:{$orgId}:logo_path")->value('value');

        $decision = request('type','approved'); // approved|waitlist|declined
        $mailable = new ApplicantDecisionMail($decision, 'Sample Opportunity', $orgName, __('This is a preview note.'), $brand, $logo);
        return $mailable->render();
    }
}
