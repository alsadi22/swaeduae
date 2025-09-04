<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class BuildSitemap extends Command
{
    protected $signature = 'swaed:site-map';
    protected $description = 'Generate public sitemap.xml (static pages + opportunities)';

    public function handle(): int
    {
        $base = config('app.url', 'https://swaeduae.ae');

        $map = Sitemap::create()
            ->add(Url::create($base . '/')->setPriority(0.8))
            ->add(Url::create($base . '/about'))
            ->add(Url::create($base . '/privacy'))
            ->add(Url::create($base . '/terms'))
            ->add(Url::create($base . '/contact'))
            ->add(Url::create($base . '/opportunities'))
            ->add(Url::create($base . '/qr/verify'));

        // Opportunities (one URL per event slug)
        $events = DB::table('events')->select('slug', 'updated_at', 'starts_at')->get();
        foreach ($events as $e) {
            if (empty($e->slug)) { continue; }
            $last = $e->updated_at ?? $e->starts_at ?? now();
            $map->add(
                Url::create($base . '/opportunities/' . $e->slug)
                    ->setLastModificationDate(Carbon::parse($last))
                    ->setPriority(0.6)
            );
            // Optional: expose ICS too. Commented to avoid noise
            // $map->add(Url::create($base . '/ics/' . $e->slug)->setLastModificationDate(Carbon::parse($last))->setPriority(0.3));
        }

        $target = public_path('sitemap.xml');
        $map->writeToFile($target);
        $this->info("Sitemap written: " . $target);

        return self::SUCCESS;
    }
}
