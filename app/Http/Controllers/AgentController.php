<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; use Illuminate\Support\Facades\File; use App\Services\AgentScanner;
class AgentController extends Controller {
  public function index(){ $j=storage_path('app/agent/report.json'); $d=File::exists($j)?json_decode(File::get($j),true):['meta'=>[],'issues'=>[],'fixes'=>[]]; return view('agent.index',['data'=>$d]); }
  public function report(){ $j=storage_path('app/agent/report.json'); $d=File::exists($j)?json_decode(File::get($j),true):['meta'=>[],'issues'=>[],'fixes'=>[]]; return response()->json($d); }
  public function scan(Request $req, AgentScanner $s){ $fix=(bool)$req->boolean('fix',false); $r=$s->scan(['fix'=>$fix,'web'=>true]); return response()->json($r); }
  public function apply(AgentScanner $s){ abort_unless(config('agent.allow_apply'),403); return response()->json($s->applyLatest()); }
  public function revert(AgentScanner $s){ abort_unless(config('agent.allow_apply'),403); return response()->json($s->revertLatest()); }
}
