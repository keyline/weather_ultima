<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_about_page_displays_document_content_and_all_northstar_places(): void
    {
        $response = $this->get(route('about'))->assertOk()->assertViewIs('about');
        foreach (['intro', 'founder'] as $section) {
            foreach (config('about.'.$section.'.paragraphs') as $paragraph) {
                $response->assertSee($paragraph);
            }
        }
        foreach (['mission', 'vision'] as $section) {
            foreach (config('about.'.$section.'.cards') as $card) {
                $response->assertSee($card['title']);
                foreach ($card['paragraphs'] as $paragraph) {
                    $response->assertSee($paragraph);
                }
                $this->assertFileExists(public_path($card['image']));
            }
        }
        foreach (config('about.team') as $member) {
            $response->assertSee($member['name'])->assertSee($member['role']);
            foreach ($member['paragraphs'] as $paragraph) {
                $response->assertSee($paragraph);
            }
            $this->assertFileExists(public_path($member['image']));
        }
        $response->assertSee('Northstar')->assertSee('wx-page-banner--left', false)
            ->assertSee('navbar', false)->assertSee('wx-footer', false);
        $this->assertSame(16, substr_count($response->getContent(), 'class="wx-about-northstar"'));
        foreach (config('about.northstar') as $person) {
            $this->assertFileExists(public_path($person['image']));
        }
        $this->assertFileExists(public_path(config('about.intro.image')));
        $this->assertFileExists(public_path(config('about.founder.image')));
    }

    public function test_editable_content_is_escaped(): void
    {
        config(['about.northstar.0.name' => '<script>alert("name")</script>']);
        $this->get(route('about'))->assertOk()
            ->assertSee('<script>alert("name")</script>')
            ->assertDontSee('<script>alert("name")</script>', false);
    }
}
