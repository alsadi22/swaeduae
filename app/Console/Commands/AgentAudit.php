<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class AgentAudit extends Command
{
    protected $signature = 'agent:audit {--json}';
    protected $description = 'Emit a health/audit report similar to _tools/agent_check.sh';

    public function handle()
    {
        $report = [
            'env' => [
                'APP_ENV'   => config('app.env'),
                'APP_DEBUG' => (bool) config('app.debug'),
                'APP_URL'   => config('app.url'),
            ],
            'routes' => [
                'home'          => Route::has('home'),
                'about'         => Route::has('about'),
                'services'      => Route::has('services'),
                'contact_show'  => Route::has('contact.show'),
                'contact_send'  => Route::has('contact.send'),
            ],
            'mail' => [
                'mailer'  => config('mail.default') ?? config('mail.mailer'),
                'host'    => config('mail.mailers.smtp.host') ?? env('MAIL_HOST'),
                'from'    => config('mail.from.address'),
            ],
        ];

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT));
        } else {
            $this->table(
                ['Key','Value'],
                collect($report)->flatMap(function($v,$k){
                    if (is_array($v)) return collect($v)->mapWithKeys(fn($vv,$kk)=>["$k.$kk"=>$vv]);
                    return [$k=>$v];
                })->map(fn($v,$k)=>[$k,is_bool($v)?($v?'true':'false'):$v])->values()
            );
        }
        return self::SUCCESS;
    }
}
