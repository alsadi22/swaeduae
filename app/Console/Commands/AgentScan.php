<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\AgentScanner;

class AgentScan extends Command
{
    protected $signature = 'agent:scan {--fix : Apply safe fixes (CLI allowed)}';
    protected $description = 'Run full Agent scan (with optional fixes)';

    public function handle(AgentScanner $scanner)
    {
        $r = $scanner->scan(['fix'=>(bool)$this->option('fix')]);
        $this->info('Report: storage/app/agent/report.json');
        $this->line(json_encode([
            'issues'=>count($r['issues'] ?? []),
            'fixes' =>count($r['fixes'] ?? []),
            'crawler_pages'=>data_get($r,'crawl.pages',0),
            'broken_links'=>count(data_get($r,'crawl.broken',[])),
        ], JSON_PRETTY_PRINT));
        return self::SUCCESS;
    }
}
