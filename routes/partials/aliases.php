
use Illuminate\Support\Facades\Route;

// /qr/verify alias to your existing verification
Route::get('/qr/verify/{code?}', function (?string $code = null) {
    if ($code) {
        return redirect()->route('verify.show', ['code' => $code]);
    }
    if (function_exists('view') && view()->exists('public.certificates.verify')) {
        return view('public.certificates.verify');
    }
    if (function_exists('view') && view()->exists('verify.index')) {
        return view('verify.index');
    }
    return redirect('/');
})->name('qr.verify');
