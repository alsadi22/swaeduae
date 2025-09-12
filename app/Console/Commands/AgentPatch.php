<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\AgentScanner;

class AgentPatch extends Command
{
    protected $signature = 'agent:patch {--apply} {--revert} {--git : auto-create branch & commit} {--push : also push}';
    protected $description = 'Apply or revert the latest staged patches (optionally commit via git)';

    public function handle(AgentScanner $scanner)
    {
        if ($this->option('apply')) {
            $r = $scanner->applyLatest(['git'=>$this->option('git'), 'push'=>$this->option('push')]);
            $this->info('Applied: '.($r['applied'] ?? 0).' files from '.($r['dir'] ?? 'n/a'));
            if (!empty($r['git'])) $this->line('Git: '.json_encode($r['git']));
        } elseif ($this->option('revert')) {
            $r = $scanner->revertLatest();
            $this->info('Reverted: '.($r['reverted'] ?? 0).' files from '.($r['dir'] ?? 'n/a'));
        } else {
            $this->warn('Choose --apply or --revert');
        }
        return self::SUCCESS;
    }
}
