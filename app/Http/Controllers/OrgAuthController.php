<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; use App\Models\Organization; use Illuminate\Support\Facades\Storage; use Illuminate\Support\Str;
class OrgAuthController extends Controller {
  public function showLogin() { return view()->exists('org.login') ? view('org.login') : (view()->exists('org.auth.login') ? view('org.auth.login') : response('Org login',200)); }
  public function performLogin(Request $r) {
    $data = $r->validate(['email'=>'required|email']);
    $org = Organization::where('email', strtolower($data['email']))->first();
    if (!$org) return back()->withErrors(['email'=>__('Organization not found')])->withInput();
    session(['org_id'=>$org->id]);
    return view('org.login');
  }
  public function showRegister() { return view()->exists('org.register') ? view('org.register') : (view()->exists('org.auth.register') ? view('org.auth.register') : response('Org register',200)); }
  public function submitRegister(Request $r) {
    $r->validate([
      'name'=>'required|string|max:160',
      'email'=>'required|email|max:160',
      'logo'=>'nullable|file|mimetypes:image/png,image/jpeg|max:5120',
      'license'=>'nullable|file|mimetypes:application/pdf,image/png,image/jpeg|max:10240',
    ]);
    $email = strtolower($r->input('email'));
    $domain = substr(strrchr($email,'@')?:'',1);
    $free = ['gmail.com','yahoo.com','outlook.com','hotmail.com','aol.com','icloud.com','proton.me','protonmail.com','live.com','msn.com','yandex.com','mail.ru'];
    if (in_array($domain,$free,true)) return back()->withErrors(['email'=>__('Please use a business email.')])->withInput();
    $org = Organization::firstOrCreate(['email'=>$email], [
      'name'=>$r->input('name'),
      'status'=>'pending',
    ]);
    $base = 'orgs/'.$org->id;
    if ($r->hasFile('logo')) {
      $p = $r->file('logo')->store($base, 'public');
      $org->logo_path = $p;
    }
    if ($r->hasFile('license')) {
      $p = $r->file('license')->store($base, 'public');
      $org->license_path = $p;
    }
    if (property_exists($org,'approved') && is_null($org->approved)) { $org->approved = false; }
    $org->status = 'pending';
    $org->save();
    session(['org_id'=>$org->id]);
    return redirect('/org/pending')->with('status', __('Submitted for approval.'));
  }
  public function pending() { return view()->exists('org.pending') ? view('org.pending') : response('Pending approval',200); }
}
