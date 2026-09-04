<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_unknown_urls_render_the_branded_404_page(): void
    {
        $response = $this->get('/no-such-page-'.uniqid());

        $response->assertNotFound();
        $response->assertSee('Page Not Found');
        $response->assertSee('404');
        // Site chrome is present.
        $response->assertSee('wx-header-fixed', false);
        $response->assertSee('wx-footer', false);
        $response->assertSee(route('home'));
    }
}
