<?php
namespace Tests\Feature;
use Tests\TestCase;
class PublicPagesTest extends TestCase {
    public function test_public_pages_reachable() {
        $this->get('/')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
        $this->get('/services')->assertStatus(200);
        $this->get('/contact-us')->assertStatus(200);
    }
}
