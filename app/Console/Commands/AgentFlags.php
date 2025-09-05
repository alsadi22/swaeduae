<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;

class AgentFlags extends Command
{
    protected $signature = 'agent:flags
        {--show : Show current flags}
        {--enable= : true/false for AGENT_ENABLED}
        {--allow-apply= : true/false for AGENT_ALLOW_APPLY}
        {--rotate-token : Generate a new AGENT_TOKEN}';
    protected $description = 'Admin safety switch for Agent flags (CLI only)';

    public function handle()
    {
        $envPath = base_path('.env');
        $env = is_readable($envPath) ? file_get_contents($envPath) : '';
        $set = function(string $key, string $val) use (&$env) {
            if (preg_match('/^'.$key.'=.*/m', $env)) { $env = preg_replace('/^'.$key.'=.*/m', $key.'='.$val, $env); }
            else { $env .= PHP_EOL.$key.'='.$val; }
        };

        if ($this->option('rotate-token')) {
            $set('AGENT_TOKEN', bin2hex(random_bytes(16)));
        }
        if (($v = $this->option('enable')) !== null) {
            $set('AGENT_ENABLED', filter_var($v, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false');
        }
        if (($v = $this->option('allow-apply')) !== null) {
            $set('AGENT_ALLOW_APPLY', filter_var($v, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false');
        }

        if (!$this->option('show')) {
            file_put_contents($envPath, $env);
            \Artisan::call('config:cache');
            $this->info('Flags updated & config cached.');
        }

        $this->line('AGENT_ENABLED='.env('AGENT_ENABLED','false'));
        $this->line('AGENT_ALLOW_APPLY='.env('AGENT_ALLOW_APPLY','false'));
        $this->line('AGENT_TOKEN='.substr(env('AGENT_TOKEN',''),0,8).'…');
        return self::SUCCESS;
    }
}
