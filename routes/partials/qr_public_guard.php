
use Illuminate\Support\Facades\Route;

// If someone hits /opportunities/qr unauthenticated, send them to admin login
Route::get('/opportunities/qr', fn() => redirect()->route('admin.login'))->name('opportunities.qr.safe');
