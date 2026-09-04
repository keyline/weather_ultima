<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_products_page_lists_active_products_from_the_database(): void
    {
        $visible = Product::factory()->create(['name' => 'SkyWatch Station', 'short_description' => 'Live rooftop weather station.']);
        Product::factory()->inactive()->create(['name' => 'Retired Widget']);

        $response = $this->get(route('products'));

        $response->assertOk();
        $response->assertSee('SkyWatch Station');
        $response->assertSee('Live rooftop weather station.');
        $response->assertDontSee('Retired Widget');
        $response->assertSee(route('products.enquiry', $visible), false);
        $response->assertDontSee('Lorem Ipsum');
    }

    public function test_products_page_shows_an_empty_state_when_there_are_no_products(): void
    {
        $this->get(route('products'))
            ->assertOk()
            ->assertSee('Products coming soon');
    }

    public function test_enquire_button_carries_the_product_context(): void
    {
        $product = Product::factory()->create(['name' => 'Anemometer']);

        $this->get(route('products'))
            ->assertSee('data-product-name="Anemometer"', false)
            ->assertSee('data-enquiry-url="'.route('products.enquiry', $product).'"', false);
    }
}
