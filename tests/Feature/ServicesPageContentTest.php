<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ServicesPageContentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_only_enabled_services_render_in_display_order(): void
    {
        Service::factory()->create(['name' => 'Second Service', 'body' => 'B body.', 'display_order' => 2]);
        Service::factory()->create(['name' => 'First Service', 'body' => 'A body.', 'display_order' => 1]);
        Service::factory()->disabled()->create(['name' => 'Hidden Service', 'body' => 'Hidden.']);

        $response = $this->get(route('services'));

        $response->assertOk();
        $response->assertSeeInOrder(['First Service', 'Second Service']);
        $response->assertDontSee('Hidden Service');
    }

    public function test_tab_panel_and_accordion_share_the_service_slug(): void
    {
        $service = Service::factory()->create(['name' => 'SkyWatch Live', 'body' => 'Body text.']);

        $html = $this->get(route('services'))->assertOk()->getContent();

        $this->assertStringContainsString('data-service-target="'.$service->slug.'"', $html);
        $this->assertStringContainsString('id="service-'.$service->slug.'"', $html);
        $this->assertStringContainsString('data-service-body="'.$service->slug.'"', $html);
        $this->assertStringContainsString('wx-service-panel', $html);
        $this->assertStringContainsString('wx-service-tab', $html);
    }

    public function test_service_body_paragraphs_and_images_render(): void
    {
        $service = Service::factory()->create([
            'name' => 'StationCraft',
            'statement' => 'Your Location. Your Data.',
            'body' => "Para one.\n\nPara two.",
            'result' => 'The result: solid data.',
        ]);
        $service->images()->create(['image' => 'services/x.png', 'alt_text' => 'A station', 'display_order' => 1]);

        $response = $this->get(route('services'));

        $response->assertSee('Para one.');
        $response->assertSee('Para two.');
        $response->assertSee('Your Location. Your Data.');
        $response->assertSee('The result: solid data.');
        $response->assertSee('wx-service-media-tile', false);
        $response->assertSee('A station');
    }

    public function test_detail_section_is_absent_when_there_are_no_services(): void
    {
        $this->get(route('services'))
            ->assertOk()
            ->assertDontSee('wx-service-detail-section', false);
    }
}
