<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OrganizationPublicController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('organizations')) {
            return view('organizations.public.index', ['rows'=>collect(),'filters'=>[],'message'=>'No organizations table.']);
        }

        $filters = [
            'q'      => trim((string)$request->get('q')),
            'emirate'=> trim((string)$request->get('emirate')),
        ];

        $q = DB::table('organizations as o')->select('o.*');

        if ($filters['q'] !== '') {
            $like = '%'.$filters['q'].'%';
            $q->where(function($w) use ($like){
                $w->where('o.name','like',$like);
                foreach (['title','about','description'] as $c) {
                    if (Schema::hasColumn('organizations',$c)) $w->orWhere("o.$c",'like',$like);
                }
            });
        }

        $emirCol = null;
        foreach (['emirate','region','city','location'] as $c) {
            if (Schema::hasColumn('organizations',$c)) { $emirCol = $c; break; }
        }
        if ($emirCol && $filters['emirate'] !== '') $q->where("o.$emirCol", $filters['emirate']);

        $q->orderBy('o.id','desc');
        $rows = $q->paginate(12)->appends($request->query());

        $emirates = [];
        if ($emirCol) {
            $emirates = DB::table('organizations')->whereNotNull($emirCol)->where($emirCol,'<>','')
                        ->select($emirCol.' as name')->distinct()->orderBy($emirCol)->pluck('name')->all();
        }

        return view('organizations.public.index', ['rows'=>$rows,'filters'=>$filters,'emirates'=>$emirates]);
    }

    public function show(Request $request, $id, $slug = null)
    {
        if (!Schema::hasTable('organizations')) abort(404);
        $o = DB::table('organizations')->where('id',$id)->first();
        abort_unless($o, 404);

        $name = $o->name ?? ($o->title ?? 'Organization');
        $want = Str::slug($name);
        if ($slug !== $want) {
            return redirect()->route('orgs.public.show', ['id'=>$id,'slug'=>$want]);
        }

        // fetch related opportunities if column exists
        $opps = collect();
        $fkCols = array_filter(['organization_id','org_id'], fn($c)=>Schema::hasColumn('opportunities',$c));
        if (Schema::hasTable('opportunities') && $fkCols) {
            $fk = $fkCols[0];
            $opps = DB::table('opportunities')->where($fk, $id)->orderBy('id','desc')->limit(20)->get();
        }

        return view('organizations.public.show', ['o'=>$o,'name'=>$name,'opps'=>$opps]);
    }
}
