<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use Illuminate\Support\Facades\File;

class CertComputeChecksums extends Command
{
    protected $signature = 'cert:rehash-missing';
    protected $description = 'Compute missing SHA-256 checksums for existing certificates';

    public function handle()
    {
        $done = 0; $skip = 0;
        Certificate::whereNull('checksum')->orWhere('checksum','')->chunk(100, function($batch) use (&$done,&$skip) {
            foreach ($batch as $c) {
                $path = public_path(ltrim($c->file_path ?? '', '/'));
                if ($c->file_path && File::exists($path)) {
                    $c->checksum = hash_file('sha256', $path);
                    $c->save();
                    $done++;
                } else {
                    $skip++;
                }
            }
        });
        $this->info("Checksums computed: {$done}, skipped: {$skip}");
        return 0;
    }
}
