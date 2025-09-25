/* Phase-0 helpers: safe stubs + stub_path (guarded). */
if (!function_exists('phase0_stub')) {
    function phase0_stub(string $name, string $uri, array $middleware = []) {
        if (!str_ends_with($name, '.stub')) { $name .= '.stub'; }
        if (!\Illuminate\Support\Facades\Route::has($name)) {
            $r = \Illuminate\Support\Facades\Route::any($uri, function() use ($name) {
                return response()->view('public.not-implemented', ['name' => $name], 200);
            })->name($name);
            if (!empty($middleware)) { $r->middleware($middleware); }
        }
    }
}
if (!function_exists('stub_path')) {
    function stub_path($prefix, $name) {
        $slug = \Illuminate\Support\Str::of($name)->replace('.', '/')->replace('_','-');
        return "/{$prefix}/".$slug;
    }
}
