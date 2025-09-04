<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SmokeTest extends DuskTestCase
{
    /** @test */
    public function public_pages_are_up()
    {
        $base = rtrim(env('APP_URL','https://swaeduae.ae'),'/');
        $this->browse(function (Browser $browser) use ($base) {
            $browser->visit($base.'/')->assertPresent('body');
            $browser->visit($base.'/login')->assertPresent('body');
            $browser->visit($base.'/register')->assertPresent('body');
            $browser->visit($base.'/qr/verify')->assertPresent('body');
        });
    }

    /** @test */
    public function guarded_pages_redirect_to_login()
    {
        $base = rtrim(env('APP_URL','https://swaeduae.ae'),'/');
        $this->browse(function (Browser $browser) use ($base) {
            $browser->visit($base.'/applications');
            $this->assertStringContainsString('login', strtolower($browser->driver->getCurrentURL()));
        });
    }
}
