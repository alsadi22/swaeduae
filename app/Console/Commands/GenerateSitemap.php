<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    protected $signature = 'app:generate-sitemap {--base-url=}';
    protected $description = 'Generate sitemap.xml into public/ (uses Spatie Sitemap)';

    public function handle(): int
    {
        $base = $this->option('base-url') ?: config('app.url');
        if (!$base) {
            $this->error('APP_URL not set and --base-url not provided.');
            return self::FAILURE;
        }
        SitemapGenerator::create($base)->writeToFile(public_path('sitemap.xml'));
        $this->info("Sitemap generated: ".public_path('sitemap.xml')." for {$base}");
        return self::SUCCESS;
    }
}
