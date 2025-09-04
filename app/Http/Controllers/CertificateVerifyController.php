<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class CertificateVerifyController extends Controller
{
  public function form(){ return view('public/certificates/verify'); }
  public function check($code){
    $row = DB::table('certificates')->where('code',$code)->orWhere('uuid',$code)->first();
    return view('public/certificates/verify', ['cert'=>$row,'code'=>$code]);
  }
}
