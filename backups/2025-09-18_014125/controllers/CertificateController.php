<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $q = DB::table('certificates')->where('user_id', $userId);

        if (Schema::hasTable('opportunities')
            && Schema::hasColumn('opportunities','title')
            && Schema::hasColumn('certificates','opportunity_id')) {
            $q->leftJoin('opportunities','opportunities.id','=','certificates.opportunity_id')
              ->addSelect('opportunities.title as opportunity_title');
        }

        $q->addSelect('certificates.id','certificates.code','certificates.title',
                      'certificates.file_path','certificates.issued_at','certificates.issued_date','certificates.hours');

        if (Schema::hasColumn('certificates','issued_at')) {
            $q->orderByDesc('certificates.issued_at');
        } elseif (Schema::hasColumn('certificates','issued_date')) {
            $q->orderByDesc('certificates.issued_date');
        } else {
            $q->orderByDesc('certificates.id');
        }

        $items = $q->paginate(15);

        return view('certificates.index', compact('items'));
    }
}
