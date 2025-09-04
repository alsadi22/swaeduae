<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use Illuminate\Support\Str;

class CertNormalizePaths extends Command
{
    protected $signature = 'cert:normalize-paths';
    protected $description = 'Normalize certificate file_path values and fix storage-prefixed paths';

    public function handle()
    {
        $count = 0;
        Certificate::chunk(200, function ($batch) use (&$count) {
            foreach ($batch as $c) {
                $old = $c->file_path;
                if (!$old) continue;
                $new = '/certificates/' . ltrim(str_replace(['/storage/certificates/','/certificates//'], ['/certificates/','/certificates/'], $old), '/');
                if ($new !== $old) {
                    $c->file_path = $new;
                    $c->save();
                    $count++;
                }
            }
        });
        $this->info("Normalized {$count} records.");
        return 0;
    }
}
