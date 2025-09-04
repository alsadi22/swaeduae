<?php
namespace App\Services;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

class AgentScanner
{
    public function scan(array $opts = []): array
    {
        $apply    = !empty($opts['fix']);
        $webApply = !empty($opts['web']);
        if ($webApply && $apply && !config('agent.allow_apply')) { $apply = false; }

        $started = now();
        $issues=[]; $fixes=[]; $notes=[];

        $notes[] = 'APP_ENV='.(Config::get('app.env') ?? 'n/a');
        if (Config::get('app.debug')) { $issues[] = ['level'=>'warn','code'=>'env.debug','msg'=>'APP_DEBUG is true']; }
        $mailer = Config::get('mail.default') ?? Config::get('mail.mailer');
        if ($mailer === 'log') { $notes[] = 'MAIL_MAILER=log (no real emails)'; }

        $routes = collect(Route::getRoutes()->getRoutes());
        $byName = []; $byKey = [];
        foreach ($routes as $r) {
            $name = $r->getName();
            if ($name) { $byName[$name][] = $r->uri(); }
            $sig = implode('|', $r->methods()).' '.$r->uri();
            $byKey[$sig] = ($byKey[$sig] ?? 0) + 1;
        }
        foreach ($byName as $name => $uris) {
            if (count($uris) > 1) {
                $issues[] = ['level'=>'warn','code'=>'route.dup_name','msg'=>"Duplicate route name '{$name}'",'data'=>$uris];
            }
        }
        foreach ($byKey as $sig => $count) {
            if ($count > 1) {
                $issues[] = ['level'=>'warn','code'=>'route.dup_signature','msg'=>"Duplicate route signature {$sig}",'data'=>['count'=>$count]];
            }
        }

        $notes[] = 'scan_ms='.now()->diffInMilliseconds($started);
        return compact('apply','webApply','issues','fixes','notes','started');
    }
}
