<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificateVerifyController extends Controller
{
    public function index()
    {
        return view('verify.index');
    }

    public function show($code)
    {
        $c = DB::table('certificates as c')
            ->select('c.*','u.name as user_name','o.title as opportunity_title')
            ->leftJoin('users as u','u.id','=','c.user_id')
            ->leftJoin('opportunities as o','o.id','=','c.opportunity_id')
            ->where('c.code',$code)->first();

        return view('verify.show', ['c'=>$c,'code'=>$code]);
    }
}
