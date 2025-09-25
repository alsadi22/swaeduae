use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicOpportunityController;

Route::get('/ics/{slug}', [PublicOpportunityController::class,'ics'])
    ->name('opportunities.ics.alt');
