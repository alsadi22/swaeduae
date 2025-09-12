<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use App\Http\Middleware\EnforceOrgRegistration;

class SwaeEnforceOrgRegisterServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void
    {
        $router->aliasMiddleware('swaed.enforceOrgRegister', EnforceOrgRegistration::class);
        // Add to 'web' group; middleware itself only triggers on POST /org/register
        $router->pushMiddlewareToGroup('web', 'swaed.enforceOrgRegister');
    }
}
