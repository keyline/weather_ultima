<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TestimonialDisplayTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_homepage_shows_only_enabled_testimonials_in_display_order(): void
    {
        Testimonial::factory()->create(['name' => 'Second Voice', 'review' => 'Came second in order.', 'display_order' => 2]);
        Testimonial::factory()->create(['name' => 'First Voice', 'review' => 'Came first in order.', 'display_order' => 1]);
        Testimonial::factory()->disabled()->create(['name' => 'Hidden Voice', 'review' => 'Should never show.']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('First Voice');
        $response->assertSee('Second Voice');
        $response->assertDontSee('Hidden Voice');
        $response->assertSeeInOrder(['First Voice', 'Second Voice']);
        $response->assertDontSee('Shankar Chatterjee');
    }

    public function test_testimonial_section_is_hidden_when_there_are_none(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('What Our Clients Say');
    }

    public function test_role_line_combines_designation_and_company(): void
    {
        Testimonial::factory()->create([
            'name' => 'Combined Role',
            'designation' => 'Director',
            'company' => 'Acme Co',
        ]);

        $this->get(route('home'))->assertSee('Director, Acme Co');
    }
}
