<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Opportunity;

class WishlistController extends Controller
{
    public function __construct(){ $this->middleware(['auth','verified']); }

    public function toggle(Request $r, Opportunity $opportunity)
    {
        $uid = $r->user()->id;
        $exists = DB::table('saved_opportunities')
            ->where('user_id',$uid)->where('opportunity_id',$opportunity->id)->exists();

        if ($exists) {
            DB::table('saved_opportunities')->where('user_id',$uid)->where('opportunity_id',$opportunity->id)->delete();
            return back()->with('success', __('Removed from saved.'));
        } else {
            DB::table('saved_opportunities')->insert([
                'user_id'=>$uid, 'opportunity_id'=>$opportunity->id, 'created_at'=>now(), 'updated_at'=>now()
            ]);
            return back()->with('success', __('Saved to your list.'));
        }
    }
}
