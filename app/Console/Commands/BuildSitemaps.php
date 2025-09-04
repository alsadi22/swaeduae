<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BuildSitemaps extends Command
{
    protected $signature = 'swaed:build-sitemaps';
    protected $description = 'Build sitemap index and children from DB';

    public function handle()
    {
        $base  = config('app.url') ?? 'https://swaeduae.ae';
        $today = now()->toAtomString();
        $dir   = public_path('sitemaps');
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $makeUrlset = function(array $rows) use ($base, $today) {
            $body = "";
            foreach ($rows as [$path,$freq,$prio]) {
                $body .= "  <url>\n".
                         "    <loc>{$base}{$path}</loc>\n".
                         "    <lastmod>{$today}</lastmod>\n".
                         "    <changefreq>{$freq}</changefreq>\n".
                         "    <priority>{$prio}</priority>\n".
                         "  </url>\n";
            }
            return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                   "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$body}</urlset>\n";
        };

        // Public urls
        $public = [
            ['/',              'daily',  '1.0'],
            ['/opportunities', 'hourly', '0.9'],
            ['/login',         'weekly', '0.3'],
            ['/register',      'weekly', '0.3'],
            ['/qr/verify',     'weekly', '0.4'],
            ['/about',         'monthly','0.1'],
            ['/privacy',       'monthly','0.1'],
            ['/terms',         'monthly','0.1'],
            ['/contact',       'monthly','0.2'],
        ];

        // Opportunities from DB
        $slugs = DB::table('opportunities')->pluck('slug')->filter()->values();
        $opps  = $slugs->map(fn($s)=>["/opportunities/{$s}", "hourly", "0.9"])->all();

        file_put_contents($dir.'/public.xml',        $makeUrlset($public));
        file_put_contents($dir.'/opportunities.xml', $makeUrlset($opps));

        // Sitemap index
        $index = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                 "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n".
                 "  <sitemap><loc>{$base}/sitemaps/public.xml</loc><lastmod>{$today}</lastmod></sitemap>\n".
                 "  <sitemap><loc>{$base}/sitemaps/opportunities.xml</loc><lastmod>{$today}</lastmod></sitemap>\n".
                 "</sitemapindex>\n";

        file_put_contents($dir.'/sitemap-index.xml', $index);

        // Ensure public/sitemap.xml points to the index
        @unlink(public_path('sitemap.xml'));
        @symlink('sitemaps/sitemap-index.xml', public_path('sitemap.xml'));

        $this->info('Sitemaps built.');
        return 0;
    }
}
