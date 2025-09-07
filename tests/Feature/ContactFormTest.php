<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;

class ContactFormTest extends TestCase
{
    #[Test]
    public function contact_page_loads_and_contains_csrf_field(): void
    {
        $res = $this->get(route('contact.get'));
        $res = $res->isRedirection() ? $this->followRedirects($res) : $res;

        $res->assertOk()->assertSee('name="_token"', false);
    }

    #[Test]
    public function contact_post_without_csrf_is_rejected(): void
    {
        $res = $this->post(route('contact.send'), [
            'name'    => 'John Tester',
            'email'   => 'john@example.com',
            'message' => 'Hello',
        ]);
        $res->assertStatus(419);
    }

    #[Test]
    public function contact_post_with_valid_csrf_redirects_back_ok(): void
    {
        Mail::fake();

        $token = 'test_token';
        $res = $this->withSession(['_token' => $token])->post(route('contact.send'), [
            '_token'  => $token,
            'name'    => 'Jane Tester',
            'email'   => 'jane@example.com',
            'message' => 'Hello',
        ]);

        $res->assertRedirect('/contact#thanks');
        $res->assertSessionHasNoErrors();
        $res->assertSessionHas('status');
        $res->assertSessionHas('clear', true);

        $html = $this->followRedirects($res);
        $html->assertSee('id="thanks"', false);
        $html->assertDontSee('value="Jane Tester"', false);
        $html->assertDontSee('value="jane@example.com"', false);
        $html->assertDontSee('Hello');
        // Mail::assertSent(...); // add if you assert specific mailable
    }
}
