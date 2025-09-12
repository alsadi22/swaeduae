<?php
namespace App\Providers;



use App\Models\Opportunity;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {

        // SWAED view composers
        View::composer(['categories.index','partials.opps_filter_bar','partials.nav'], function ($view) {
            $cats = collect(); $regions = collect(); $latest = collect();
            try {
                $cats = Opportunity::query()
                    ->whereNotNull('category')->select('category')->distinct()->orderBy('category')->pluck('category');
            } catch (\Throwable $e) {}
            try {
                $regions = Opportunity::query()
                    ->whereNotNull('region')->select('region')->distinct()->orderBy('region')->pluck('region');
            } catch (\Throwable $e) {}
            try {
                $latest = Opportunity::query()->orderByDesc('created_at')->limit(5)->get();
            } catch (\Throwable $e) {}

            $view->with('cats', $cats)
                 ->with('regions', $regions)
                 ->with('navLatestOpportunities', $latest);
        });

        // Attach to the specific views that used to query in Blade
        view()->composer([
            'categories.index',
            'partials.opps_filter_bar',
        ], \App\View\Composers\OppFiltersComposer::class);
    }
}
