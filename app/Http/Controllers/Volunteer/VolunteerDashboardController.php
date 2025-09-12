<?php
namespace App\Http\Controllers\Volunteer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class VolunteerDashboardController extends Controller
{
  public function index(){
    $uid = auth()->id();
    // Simple "matching": same category or emirate weighs higher
    $profile = DB::table('profiles')->where('user_id',$uid)->first();
    $skills = $profile? json_decode($profile->skills??'[]',true):[];
    $emirate = $profile->emirate ?? null;

    $recommend = DB::table('opportunities')
      ->when($emirate, fn($x)=>$x->orderByRaw("location like ? desc",["%$emirate%"]))
      ->orderByDesc('id')->limit(8)->get();

    $totalMinutes = DB::table('volunteer_hours')->where('user_id', $uid)->sum('minutes');
    $apps  = DB::table('applications')->where('user_id',$uid)->orderByDesc('id')->limit(8)->get();

    return view('volunteer/dashboard', compact('recommend','totalMinutes','apps'));
  }
}
