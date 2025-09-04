<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicOpportunityController extends Controller
{
  public function index(Request $r){
    $q = trim((string)$r->get('q',''));
    $cat = (int)$r->get('category_id',0);
    $emirate = trim((string)$r->get('emirate',''));
    $rows = DB::table('opportunities')
      ->when($q!=='' , fn($x)=>$x->where('title','like',"%$q%")->orWhere('location','like',"%$q%"))
      ->when($cat>0 , fn($x)=>$x->where('category_id',$cat))
      ->when($emirate!=='', fn($x)=>$x->where('emirate',$emirate))
      ->orderByDesc('id')->paginate(12)->withQueryString();
    $cats = DB::table('categories')->orderBy('name')->get();
    return view('public/opportunities/index', compact('rows','q','cat','cats','emirate'));
  }

  public function show($id){
    $item = DB::table('opportunities')->where('id',$id)->first();
    abort_unless($item,404);
    $applied = auth()->check()
      ? DB::table('applications')->where(['user_id'=>auth()->id(),'opportunity_id'=>$id])->first()
      : null;
    return view('public/opportunities/show', compact('item','applied'));
  }
}
