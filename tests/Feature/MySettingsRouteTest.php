<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use App\Models\User;

class MySettingsRouteTest extends TestCase
{
    /** @test */
    public function guests_are_redirected_to_login()
    {
        ->get(/my/settings)->assertRedirect(/login);
    }

    /** @test */
    public function authed_user_can_render_my_settings_view()
    {
         = User::factory()->create();
        ->actingAs()
             ->get(/my/settings)
             ->assertStatus(200)
             ->assertSee("Settings"); // adjust if your view text differs
    }

    /** @test */
    public function route_is_registered_via_get_not_view_chaining()
    {
         = collect(Route::getRoutes())->first(fn() => ->uri() === "my/settings");
        ->assertNotNull(, "Route /my/settings not registered");
        ->assertSame(["GET","HEAD"], ->methods());
    }
}
