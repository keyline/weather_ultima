<?php

namespace Tests\Feature;

use App\Models\BrandLogo;
use App\Models\CoreValue;
use App\Models\DimensionCard;
use App\Models\HomeSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class HomePageContentTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_homepage_renders_dynamic_banner_and_founder_content(): void
    {
        HomeSetting::query()->create([
            'banner_title' => 'Weather, Understood',
            'banner_subtitle' => 'Guidance for every decision.',
            'founder_name' => 'Dr. A. Sharma',
            'founder_designation' => 'Chief Meteorologist',
            'founder_intro' => 'A lifelong student of the sky.',
            'founder_description' => "Para one here.\n\nPara two here.",
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Weather, Understood')
            ->assertSee('Guidance for every decision.')
            ->assertSee('Dr. A. Sharma')
            ->assertSee('Para one here.')
            ->assertSee('Para two here.')
            ->assertSee('wx-dim-head', false)
            ->assertSee('wx-founder-card', false);
    }

    public function test_only_enabled_brand_logos_appear_in_display_order(): void
    {
        BrandLogo::factory()->create(['alt_text' => 'Second Outlet', 'display_order' => 2]);
        BrandLogo::factory()->create(['alt_text' => 'First Outlet', 'display_order' => 1]);
        BrandLogo::factory()->disabled()->create(['alt_text' => 'Hidden Outlet']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['First Outlet', 'Second Outlet'])
            ->assertDontSee('Hidden Outlet')
            ->assertSee('wx-media-logo', false);
    }

    public function test_only_enabled_core_values_appear_in_display_order(): void
    {
        CoreValue::factory()->create(['icon' => 'A', 'title' => 'Accuracy', 'display_order' => 2]);
        CoreValue::factory()->create(['icon' => 'R', 'title' => 'Reliability', 'display_order' => 1]);
        CoreValue::factory()->disabled()->create(['title' => 'Secret Value']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['Reliability', 'Accuracy'])
            ->assertDontSee('Secret Value')
            ->assertSee('wx-value-row', false);
    }

    public function test_only_enabled_dimension_cards_appear_in_display_order(): void
    {
        DimensionCard::factory()->create(['title' => 'Card Two', 'display_order' => 2]);
        DimensionCard::factory()->create(['title' => 'Card One', 'display_order' => 1]);
        DimensionCard::factory()->disabled()->create(['title' => 'Hidden Card']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['Card One', 'Card Two'])
            ->assertDontSee('Hidden Card')
            ->assertSee('dim-card', false);
    }

    public function test_core_values_section_is_hidden_when_there_are_none(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Our RAINBOW Has More Than Colours', false);
    }
}
