<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAdminController extends Controller
{
    public function __construct(){
        $this->middleware(function($req,$next){
            $u = $req->user();
            abort_unless($u && ($u->hasRole('admin') || $u->can('roles.manage')), 403);
            return $next($req);
        });
    }
    public function index(){
        $roles = Role::withCount('users')->orderBy('name')->get();
        $perms = Permission::orderBy('name')->get();
        return view('admin.roles.index', compact('roles','perms'));
    }
    public function store(Request $r){
        $data = $r->validate(['name'=>'required|string|max:64']);
        Role::findOrCreate($data['name'], 'web');
        return back()->with('ok','Role created.');
    }
    public function updatePermissions(Request $r, $id){
        $role = Role::findById($id,'web');
        $perms = array_values(array_filter((array)$r->input('permissions',[])));
        $role->syncPermissions($perms);
        return back()->with('ok','Permissions updated.');
    }
    public function createPermission(Request $r){
        $data = $r->validate(['name'=>'required|string|max:64']);
        Permission::findOrCreate($data['name'],'web');
        return back()->with('ok','Permission created.');
    }
}
