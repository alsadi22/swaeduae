if (app()->environment("production")) { return; }
use Illuminate\Support\Facades\Route; use App\Http\Controllers\AgentController;
Route::middleware(['web','agent.gate'])->group(function () {
  Route::get('/_agent',             [AgentController::class,'index']);
  Route::get('/_agent/report.json', [AgentController::class,'report']);
  Route::get('/_agent/scan',        [AgentController::class,'scan']);
  Route::post('/_agent/patch/apply',[AgentController::class,'apply']);
  Route::post('/_agent/patch/revert',[AgentController::class,'revert']);
});
