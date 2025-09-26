
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    // Friendly donation page (GET)
    Route::get('/donate', function () {
        $ok = env('STRIPE_KEY') || env('PAYTABS_PROFILE_ID');
        if (!$ok) return response()->view('public.not-implemented', ['title' => 'Donations'], 200);
        return app()->handle(request()); // let real route handle if configured
    })->name('donate.form.safe');

    // Success/Cancel should never 500 – show a friendly page if not wired
    Route::get('/payment/cancel',  fn() => response()->view('public.not-implemented', ['title'=>'Payment Cancel'], 200));
    Route::get('/payment/success', fn() => response()->view('public.not-implemented', ['title'=>'Payment Success'], 200));
});
